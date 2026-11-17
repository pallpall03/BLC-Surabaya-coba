<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role->value,
            'role_label' => $this->role->label(),
            'blcs' => $this->whenLoaded('blcs', fn () => $this->blcs
                ->map(fn ($blc) => ['id' => $blc->id, 'name' => $blc->name, 'slug' => $blc->slug])
                ->values()),
        ];
    }
}
