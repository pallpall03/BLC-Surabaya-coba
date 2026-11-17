<?php

namespace App\Services\Auth;

final readonly class KantorkuProfile
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $email = null,
    ) {
    }
}
