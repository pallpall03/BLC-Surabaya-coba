<?php

namespace App\Modules\Pengguna\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenggunaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,
            'kantorku_id' => $this->kantorku_id,
            'role' => $this->role->value,
            'role_label' => $this->role->label(),
            'is_active' => $this->is_active,
            'participant_category_id' => $this->participant?->participant_category_id,
            'participant_category' => $this->whenLoaded('participant.participantCategory', fn () => $this->participant->participantCategory?->name),
            'visit_history' => $this->whenLoaded('visits', fn () => $this->visits->map(fn ($v) => [
                'id' => $v->id,
                'blc' => $v->blc?->name,
                'visitor_category' => $v->visitorCategory?->name,
                'visited_at' => $v->visited_at?->toIso8601String(),
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}