<?php


namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Store a newly created order and process stock deduction.
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = DB::transaction(function () use ($request) {
                $payload = $request->validated();
                $payload['created_by'] = auth()->id() ?? 1;
                $payload['order_no'] = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
                $payload['order_date'] = now();

                // 1. Create the Order
                $order = Order::create($payload);

                // 2. Process Items and Stock
                foreach ($request->items as $item) {
                    $product = Product::where('id', $item['product_id'])
                        ->lockForUpdate() // ការពារ Race Condition
                        ->first();

                    if (!$product) {
                        throw new \Exception("Product ID {$item['product_id']} not found.");
                    }

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->product_name}. Available: {$product->quantity}");
                    }

                    // Create Order Item
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?? 0,
                        'sub_total' => $item['sub_total'],
                    ]);

                    // Deduct stock from products table
                    $product->decrement('quantity', $item['quantity']);
                }

                return $order;
            });

            return response()->json([
                'status' => 'success',
                'data' => $order->load('items.product'),
                'message' => 'Order completed successfully.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Rollback stock if an order is cancelled/deleted.
     */
    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $order = Order::with('items')->find($id);

                if (!$order) {
                    throw new \Exception("Order not found.");
                }

                // Restore stock
                foreach ($order->items as $item) {
                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($product) {
                        $product->increment('quantity', $item->quantity);
                    }
                }

                $order->items()->delete();
                $order->delete();
            });

            return response()->json(['message' => 'Order deleted and stock restored.']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
