<?php

namespace App\Modules\Operator\Domain\Entities;

use App\Enums\UserRole;

class Operator
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $kantorkuId,
        public readonly UserRole $role = UserRole::Operator,
        public readonly bool $isActive = true,
        public readonly array $managedBlcIds = [],
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
            managedBlcIds: $user->lokasi_blc_ids ?? [],
        );
    }
}