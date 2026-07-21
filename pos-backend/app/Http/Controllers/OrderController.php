<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{


    public function index(Request $req)
    {
        $query = Order::with(['customer', 'currency', 'paymentMethod', 'items.product']);

        if ($req->filled("order_no")) {
            $query->where("order_no", "LIKE", "%" . $req->input("order_no") . "%");
        }

        $list = $query->orderBy('id', 'desc')->paginate($req->input('limit', 15));

        $products = Product::all();
        $customers = Customer::all();
        $paymentMethods = PaymentMethod::all();
        $category = Category::all();

        return response()->json([
            'list' => $list,
            'products' => $products,
            'customers' => $customers,
            'payment_methods' => $paymentMethods,
            'category' => $category,
            'total' => $list->total()
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $payload = $request->validated();
            $payload['created_by'] = auth()->id() ?? 1;
            $payload['order_no'] = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());

            $order = Order::create($payload);

            foreach ($request->items as $item) {

                // Lock product row (prevent race condition)
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new \Exception("Product not found: {$item['product_id']}");
                }

                // Stock validation
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception(
                        "Not enough stock for product ID {$product->id}"
                    );
                }

                // ➕ Create order item
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'sub_total' => $item['sub_total'],
                ]);

                // Deduct stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            return response()->json([
                'data' => $order->load('items.product'),
                'message' => 'Order created successfully and stock updated.'
            ], 201);
        });
    }

    public function show(string $id)
    {
        $order = Order::with(['items.product', 'customer', 'currency', 'paymentMethod'])
            ->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json(['data' => $order]);
    }

    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {

            $order = Order::with('items')->find($id);

            if (!$order) {
                return response()->json(['message' => 'Order not found.'], 404);
            }

            foreach ($order->items as $item) {

                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                }
            }

            $order->delete();

            return response()->json([
                'message' => 'Order deleted and stock restored.'
            ]);
        });
    }
}
