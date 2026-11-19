<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Dipakai untuk kategori peserta dan kategori pengunjung (yang kedua tidak punya deskripsi). */
class CategoryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
        ];

        if (array_key_exists('description', $this->resource->getAttributes())) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
