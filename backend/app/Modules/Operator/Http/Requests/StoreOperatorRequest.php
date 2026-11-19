<?php

namespace App\Modules\Operator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOperatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->canManageMasterData() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'kantorku_id' => 'nullable|string|max:50',
            'lokasi_blc_ids' => 'nullable|array',
            'lokasi_blc_ids.*' => 'integer|exists:blcs,id',
        ];
    }
}