<?php

namespace App\Modules\Pengguna\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public registration - no auth required
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'username' => 'nullable|string|max:50|unique:users,username',
            'participant_category_id' => 'nullable|integer|exists:participant_categories,id',
        ];
    }
}