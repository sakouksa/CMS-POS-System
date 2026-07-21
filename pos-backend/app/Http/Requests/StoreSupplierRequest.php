<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Automatically fetch structural parameters to bypass unique requirements on update
        $id = $this->route('supplier') ?? $this->input('id');

        return [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'tel' => 'required|string|unique:suppliers,tel,' . $id,
            'email' => 'nullable|email|unique:suppliers,email,' . $id,
            'address' => 'nullable|string',
            'vat_number' => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
