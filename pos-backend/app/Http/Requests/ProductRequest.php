<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',

            'product_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',

            'description' => 'nullable|string',

            'cost_price' => 'nullable|numeric',
            'price' => 'required|numeric',
            'discount_percent' => 'nullable|integer|min:0|max:100',

            'stock_quantity' => 'required|integer',
            'min_stock_alert' => 'nullable|integer',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',

            'status' => 'required|in:0,1',
            'is_featured' => 'nullable|in:0,1,true,false',

            'weight' => 'nullable|numeric',
        ];
    }
}
