<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Membuat akun admin awal dari .env (SEED_ADMIN_EMAIL dan SEED_ADMIN_PASSWORD).
     * Tidak ada password bawaan di kode; kalau variabelnya kosong, seeder dilewati.
     */
    public function run(): void
    {
        $email = env('SEED_ADMIN_EMAIL');
        $password = env('SEED_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('SEED_ADMIN_EMAIL / SEED_ADMIN_PASSWORD belum diisi, admin dilewati.');

            return;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $user->name ?: 'Admin';
        $user->password = $password;
        $user->role = UserRole::Admin;
        $user->is_active = true;
        $user->save();
    }
}
