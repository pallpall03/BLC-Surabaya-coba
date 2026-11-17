<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperadminSeeder extends Seeder
{
    /**
     * Membuat akun superadmin awal dari .env (SEED_SUPERADMIN_EMAIL dan SEED_SUPERADMIN_PASSWORD).
     * Tidak ada password bawaan di kode; kalau variabelnya kosong, seeder dilewati.
     */
    public function run(): void
    {
        $email = env('SEED_SUPERADMIN_EMAIL');
        $password = env('SEED_SUPERADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('SEED_SUPERADMIN_EMAIL / SEED_SUPERADMIN_PASSWORD belum diisi, superadmin dilewati.');

            return;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $user->name ?: 'Super Admin';
        $user->password = $password;
        $user->role = UserRole::Superadmin;
        $user->is_active = true;
        $user->save();
    }
}
