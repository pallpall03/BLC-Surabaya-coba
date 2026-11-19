<?php

namespace App\Modules\Pengguna\Domain\Entities;

use App\Enums\UserRole;

class Pengguna
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $username,
        public readonly ?string $kantorkuId,
        public readonly UserRole $role = UserRole::Pengguna,
        public readonly bool $isActive = true,
        public readonly ?int $participantCategoryId = null,
        public readonly array $visitHistory = [],
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
            participantCategoryId: $user->participant?->participant_category_id,
            visitHistory: $user->visits->map(fn ($visit) => [
                'id' => $visit->id,
                'blc_id' => $visit->blc_id,
                'blc_name' => $visit->blc?->name,
                'visitor_category_id' => $visit->visitor_category_id,
                'visited_at' => $visit->visited_at?->toIso8601String(),
            ])->toArray(),
        );
    }

    public function isParticipant(): bool
    {
        return $this->participantCategoryId !== null;
    }

    public function isVisitor(): bool
    {
        return !$this->isParticipant();
    }
}