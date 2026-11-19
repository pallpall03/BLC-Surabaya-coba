<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KoordinatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'kantorku_id' => $this->kantorku_id,
            'role' => $this->role?->value,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'lokasi_blc_ids' => $this->whenLoaded('blcs', function () {
                return $this->blcs->pluck('id')->all();
            }),
            'blcs' => BlcResource::collection($this->whenLoaded('blcs')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}