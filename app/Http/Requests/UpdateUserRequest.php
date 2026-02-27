<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        'name' => 'sometimes|string|max:255',
        // 'email' => 'sometimes|email|unique:users,email,' . $this->id,
        'phone' => 'sometimes|max:10',
        'password' => 'nullable|min:6',
        'role' => 'sometimes|in:admin,buyer',
        'status' => 'sometimes|in:active,suspended',
    ];
}
}
