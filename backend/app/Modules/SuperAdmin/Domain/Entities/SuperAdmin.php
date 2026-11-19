<?php

namespace App\Modules\SuperAdmin\Domain\Entities;

use App\Enums\UserRole;

class SuperAdmin
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly UserRole $role = UserRole::SuperAdmin,
        public readonly bool $isActive = true,
    ) {}

    public static function fromUser(\App\Models\User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            phone: $user->phone,
            role: $user->role,
            isActive: $user->is_active,
        );
    }
}