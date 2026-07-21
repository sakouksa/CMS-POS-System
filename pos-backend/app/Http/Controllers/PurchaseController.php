<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Exports\PurchaseExport;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller implements HasMiddleware
{

    /**
     * Define permission-based middleware for the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:purchase.view', only: ['index']),
            new Middleware('permission:purchase.export', only: ['index']),
            new Middleware('permission:purchase.viewone', only: ['show']),
            new Middleware('permission:purchase.create', only: ['store']),
            new Middleware('permission:purchase.update', only: ['update']),
            new Middleware('permission:purchase.delete', only: ['destroy']),
        ];
    }

// នៅក្នុង PurchaseController.php
    public function export()
    {
        return Excel::download(new PurchaseExport, 'Purchase_List.xlsx');
    }

    /**
     * Display a listing of purchases with filters.
     */
    public function index(Request $req)
    {
        $query = Purchase::with(['supplier', 'paymentMethod', 'creator']);

        // Search by Invoice No or Reference
        if ($req->filled("txt_search")) {
            $search = $req->input("txt_search");
            $query->where(function ($q) use ($search) {
                $q->where("purchase_no", "LIKE", "%{$search}%")
                    ->orWhere("reference_no", "LIKE", "%{$search}%");
            });
        }

        // Filter by Supplier
        if ($req->filled("supplier_id")) {
            $query->where("supplier_id", $req->input("supplier_id"));
        }

        // Filter by Payment Status
        if ($req->filled("payment_status")) {
            $query->where("payment_status", $req->input("payment_status"));
        }

        $list = $query->orderBy('id', 'desc')->paginate($req->input('limit', 15));

        return response()->json([
            'list' => $list,
            'suppliers' => Supplier::where('is_active', true)->get(['id', 'name']),
            'products' => Product::get(['id', 'product_name']),
            'payment_methods' => PaymentMethod::where('is_active', true)->get(['id', 'name'])
        ]);
    }

    /**
     * Store a new purchase and update supplier balance.
     */
    public function store(StorePurchaseRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $items = collect($request->input('items'));

            // Calculations
            $totalAmount = $items->sum(fn($item) => $item['quantity'] * $item['purchase_unit_cost']);
            $discount = $request->input('discount', 0);
            $tax = $request->input('tax', 0);
            $grandTotal = ($totalAmount - $discount) + $tax;

            $paidAmount = $request->input('paid_amount', 0);
            $dueAmount = max(0, $grandTotal - $paidAmount);

            // Determine Payment Status
            $paymentStatus = 'due';
            if ($paidAmount >= $grandTotal) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            }

            // Create Purchase Header
            $purchase = Purchase::create([
                'purchase_no' => 'PO-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'reference_no' => $request->input('reference_no'),
                'supplier_id' => $request->input('supplier_id'),
                'purchase_date' => $request->input('purchase_date'),
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_method_id' => $request->input('payment_method_id'),
                'payment_status' => $paymentStatus,
                'status' => $request->input('status', 'received'),
                'created_by' => auth()->id() ?? 1,
                'description' => $request->input('description'),
            ]);

            // Bulk Insert Purchase Items (Performance Optimized)
            $purchaseItems = $items->map(function ($item) use ($purchase) {
                return [
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_unit_cost' => $item['purchase_unit_cost'],
                    'sub_total' => $item['quantity'] * $item['purchase_unit_cost'],
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            PurchaseItem::insert($purchaseItems);

            // Update Supplier Balance if there is debt
            if ($dueAmount > 0) {
                Supplier::where('id', $request->input('supplier_id'))
                    ->increment('current_balance', $dueAmount);
            }

            return response()->json([
                'data' => $purchase->load(['supplier', 'paymentMethod']),
                'message' => 'Purchase order created successfully.',
            ], 201);
        });
    }

    /**
     * Show detailed purchase information.
     */
    /**
     * Show detailed purchase information.
     */
    public function show(string $id)
    {
        $purchase = Purchase::with([
            'supplier',
            'paymentMethod',
            'creator',
            'purchase_items.product'
        ])->find($id);

        if (!$purchase) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        return response()->json(['data' => $purchase]);
    }

    /**
     * Update restricted to maintain audit integrity.
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            'message' => 'Direct updates are disabled to preserve financial integrity. Use returns or adjustments.'
        ], 403);
    }

    /**
     * Delete purchase and reverse supplier balance.
     */
    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $purchase = Purchase::find($id);

            if (!$purchase) {
                return response()->json(['message' => 'Record not found.'], 404);
            }

            // Reverse the supplier balance impact
            if ($purchase->due_amount > 0) {
                Supplier::where('id', $purchase->supplier_id)
                    ->decrement('current_balance', $purchase->due_amount);
            }

            $purchase->delete(); // Cascading deletes should handle purchase_items if set in DB

            return response()->json([
                'message' => 'Purchase transaction deleted and balance adjusted.',
            ]);
        });
    }
}
