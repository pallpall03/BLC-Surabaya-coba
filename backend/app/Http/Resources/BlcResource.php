<?php

namespace App\Http\Resources;

use App\Enums\BlcRegion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Blc */
class BlcResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'region' => $this->region,
            'region_label' => $this->region ? BlcRegion::tryFrom($this->region)?->label() : null,
            'address' => $this->address,
            'kecamatan' => $this->whenLoaded('kecamatan', fn () => $this->kecamatan
                ? ['id' => $this->kecamatan->id, 'name' => $this->kecamatan->name] : null),
            'kelurahan' => $this->whenLoaded('kelurahan', fn () => $this->kelurahan
                ? ['id' => $this->kelurahan->id, 'name' => $this->kelurahan->name] : null),
            'kecamatan_id' => $this->kecamatan_id,
            'kelurahan_id' => $this->kelurahan_id,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'phone' => $this->phone,
            'description' => $this->description,
            'photo_path' => $this->photo_path,
            'show_on_landing' => $this->show_on_landing,
            'is_active' => $this->is_active,
            'instructors_count' => $this->whenCounted('instructors'),
        ];
    }
}
