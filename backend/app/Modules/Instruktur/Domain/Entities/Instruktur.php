<?php

namespace App\Modules\Instruktur\Domain\Entities;

use App\Enums\UserRole;

class Instruktur
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $username,
        public readonly ?string $kantorkuId,
        public readonly UserRole $role = UserRole::Instruktur,
        public readonly bool $isActive = true,
        public readonly array $blcIds = [],
        public readonly array $blcs = [],
    ) {}

    public static function fromUser(\App\Models\User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            phone: $user->phone,
            username: $user->username,
            kantorkuId: $user->kantorku_id,
            role: $user->role,
            isActive: $user->is_active,
            blcIds: $user->lokasi_blc_ids ?? [],
            blcs: $user->blcs->map(fn ($blc) => [
                'id' => $blc->id,
                'name' => $blc->name,
                'slug' => $blc->slug,
            ])->toArray(),
        );
    }
}