<?php

namespace App\Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuperAdminRequest extends FormRequest
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
            'role' => 'required|in:super_admin,operator',
        ];
    }
}