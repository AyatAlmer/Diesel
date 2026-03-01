<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        // 'category_id' => 'nullable|exists:categories,id',
        'location_id' => 'required|exists:locations,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'quantity' => 'nullable|numeric|min:0',
        'condition' => 'nullable|in:new,used',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ];
}
}
