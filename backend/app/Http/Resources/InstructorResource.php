<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class InstructorResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'kantorku_id' => $this->kantorku_id,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'blcs' => $this->whenLoaded('blcs', fn () => $this->blcs
                ->map(fn ($blc) => ['id' => $blc->id, 'name' => $blc->name, 'slug' => $blc->slug])
                ->values()),
        ];
    }
}
