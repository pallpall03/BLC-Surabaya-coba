<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Satu pintu login untuk semua role.
 *  - Akun yang terhubung Kantorku (kantorku_id terisi): diperiksa ke Kantorku.
 *  - Akun lokal: diperiksa dengan password di database.
 * Akun yang tidak ada dan password salah menghasilkan hasil yang sama (null) agar
 * penyerang tidak bisa menebak username mana yang terdaftar.
 */
class CredentialAuthenticator
{
    public function __construct(private readonly KantorkuClient $kantorku)
    {
    }

    public function attempt(string $login, string $password): ?User
    {
        $needle = mb_strtolower(trim($login));

        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [$needle])
            ->orWhereRaw('LOWER(email) = ?', [$needle])
            ->first();

        if ($user === null) {
            // Menyamakan waktu respons dengan kasus user ada.
            Hash::check($password, $this->dummyHash());

            return null;
        }

        if ($user->kantorku_id !== null) {
            $profile = $this->kantorku->authenticate($user->username ?? $needle, $password);

            return ($profile !== null && hash_equals((string) $user->kantorku_id, $profile->id)) ? $user : null;
        }

        return Hash::check($password, $user->password) ? $user : null;
    }

    private function dummyHash(): string
    {
        static $hash = null;

        return $hash ??= Hash::make('dummy-password-for-timing');
    }
}
