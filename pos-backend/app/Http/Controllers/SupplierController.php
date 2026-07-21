<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Models\Supplier;
use App\Models\PaymentMethod;

// Pre-loaded to feed React frontend dropdowns
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SupplierController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:supplier.view', only: ['index']),
            new Middleware('permission:supplier.view_single', only: ['show']),
            new Middleware('permission:supplier.create', only: ['store']),
            new Middleware('permission:supplier.update', only: ['update']),
            new Middleware('permission:supplier.delete', only: ['destroy']),
        ];
    }

    /**
     * Get all suppliers with search and active filter functionality
     */
    public function index(Request $req)
    {
        $query = Supplier::query();

        // Search profile fields by corporate name, point of contact, or telephone line
        if ($req->has("txt_search") && $req->input("txt_search") !== "") {
            $search = $req->input("txt_search");
            $query->where(function ($q) use ($search) {
                $q->where("name", "LIKE", "%{$search}%")
                    ->orWhere("contact_person", "LIKE", "%{$search}%")
                    ->orWhere("tel", "LIKE", "%{$search}%");
            });
        }

        // Filter status matching active parameters
        if ($req->input("is_active") !== null && $req->input("is_active") !== "") {
            $query->where("is_active", $req->input("is_active"));
        }

        $list = $query->orderBy('id', 'desc')->get();
        $total = $query->count();

        return response()->json([
            'list' => $list,
            'total' => $total,
            'payment_methods' => PaymentMethod::where('is_active', true)->get() // Feeds frontend payment config options
        ]);
    }

    /**
     * Store a new supplier into the database
     */
    public function store(StoreSupplierRequest $request)
    {
        $payload = $request->validated();

        // Handle automated opening-to-current ledger conversion matching your schema
        if (!isset($payload['current_balance']) && isset($payload['opening_balance'])) {
            $payload['current_balance'] = $payload['opening_balance'];
        }

        $data = Supplier::create($payload);

        if (!$data) {
            return response()->json([
                'error' => [
                    'message' => 'Failed to add new supplier profile data.',
                ],
            ], 500);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Supplier registered successfully.',
        ], 201);
    }

    /**
     * Get a single supplier record by ID
     */
    public function show(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier record not found.'
            ], 404);
        }

        return response()->json([
            'data' => $supplier,
        ]);
    }

    /**
     * Update an existing supplier profile record
     */
    public function update(StoreSupplierRequest $request, string $id)
    {
        $data = Supplier::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Supplier profile record not found.'
            ], 404);
        }

        $data->update($request->validated());

        return response()->json([
            'data' => $data,
            'message' => 'Supplier profile updated successfully.',
        ]);
    }

    /**
     * Delete a supplier from the database
     */
    public function destroy(string $id)
    {
        $data = Supplier::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Supplier record not found.'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully.',
        ]);
    }
}
