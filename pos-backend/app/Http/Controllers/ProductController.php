<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:product.view', only: ['index', 'show']),
            new Middleware('permission:product.create', only: ['store']),
            new Middleware('permission:product.update', only: ['update']),
            new Middleware('permission:product.delete', only: ['destroy']),
            new Middleware('permission:product.export', only: ['export']),
        ];
    }

    public function export()
    {
        return Excel::download(new ProductExport, 'Product_List.xlsx');
    }

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->id) {
            $query->where('id', $request->id);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->txt_search) {
            $query->where('product_name', 'LIKE', "%{$request->txt_search}%");
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $products = $query->with(['category', 'brand'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            "list" => $products,
            "total" => $products->count(),
            "category" => Category::all(),
            "brand" => Brand::all(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['quantity'] = (int)($data['quantity'] ?? $data['stock_quantity'] ?? 0);
        unset($data['stock_quantity'], $data['image_remove'], $data['old_gallery']);

        $data['status'] = (int)($data['status'] ?? 1);
        $data['is_featured'] = (int)($data['is_featured'] ?? 0);

        // MAIN IMAGE
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // GALLERY (FIXED - NO json_encode)
        if ($request->hasFile('gallery')) {
            $gallery = [];

            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('products', 'public');
            }

            $data['gallery'] = $gallery;
        }

        $product = Product::create($data);

        return response()->json([
            "message" => "Product created successfully",
            "data" => $product
        ], 201);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'brand'])->find($id);

        if (!$product) {
            return response()->json(["message" => "Product not found"], 404);
        }

        return response()->json(["data" => $product]);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                "message" => "Product not found"
            ], 404);
        }

        $data = $request->validated();
        $data['quantity'] = (int)($data['quantity'] ?? $data['stock_quantity'] ?? $product->quantity ?? 0);
        unset($data['stock_quantity'], $data['image_remove'], $data['old_gallery']);

        $data['status'] = (int)($data['status'] ?? $product->status);
        $data['is_featured'] = (int)($data['is_featured'] ?? $product->is_featured);

        if ($request->hasFile('image')) {

            // delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')
                ->store('products', 'public');
        }

        // old gallery
        $gallery = $request->old_gallery ?? [];

        // upload new gallery
        if ($request->hasFile('gallery')) {

            foreach ($request->file('gallery') as $file) {

                $gallery[] = $file->store('products', 'public');
            }
        }

        $data['gallery'] = $gallery;

        $product->update($data);

        return response()->json([
            "message" => "Product updated successfully",
            "data" => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(["message" => "Product not found"], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        if ($product->gallery) {
            foreach ($product->gallery as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $product->delete();

        return response()->json([
            "message" => "Product deleted successfully"
        ]);
    }
}
