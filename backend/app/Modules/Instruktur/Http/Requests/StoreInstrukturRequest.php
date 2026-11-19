<?php

namespace App\Modules\Instruktur\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstrukturRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->canManageInstruktur() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'username' => 'required|string|max:50|unique:users,username',
            'kantorku_id' => 'nullable|string|max:50',
            'lokasi_blc_ids' => 'nullable|array',
            'lokasi_blc_ids.*' => 'integer|exists:blcs,id',
        ];
    }
}