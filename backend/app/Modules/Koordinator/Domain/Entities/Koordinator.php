<?php

namespace App\Modules\Koordinator\Domain\Entities;

use App\Enums\UserRole;

class Koordinator
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $kantorkuId,
        public readonly UserRole $role = UserRole::Koordinator,
        public readonly bool $isActive = true,
        public readonly array $lokasiBlcIds = [],
        public readonly array $blcs = [],
    ) {}

    public static function fromUser(\App\Models\User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            phone: $user->phone,
            kantorkuId: $user->kantorku_id,
            role: $user->role,
            isActive: $user->is_active,
            lokasiBlcIds: $user->lokasi_blc_ids ?? [],
            blcs: $user->blcs->map(fn ($blc) => [
                'id' => $blc->id,
                'name' => $blc->name,
                'slug' => $blc->slug,
            ])->toArray(),
        );
    }
}