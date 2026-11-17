<?php

namespace App\Services\Auth;

use App\Exceptions\KantorkuUnavailable;

/**
 * Pengganti sementara Kantorku untuk pengembangan. Satu akun percobaan dari .env.
 * Sengaja menolak jalan di luar environment local/testing agar tidak terbawa ke produksi.
 */
class FakeKantorkuClient implements KantorkuClient
{
    public function authenticate(string $username, string $password): ?KantorkuProfile
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new KantorkuUnavailable('Driver Kantorku "fake" hanya boleh dipakai di environment local/testing.');
        }

        $cfg = config('kantorku.fake');

        if (empty($cfg['username']) || empty($cfg['password'])) {
            return null;
        }

        $userOk = hash_equals(mb_strtolower((string) $cfg['username']), mb_strtolower($username));
        $passOk = hash_equals((string) $cfg['password'], $password);

        if (! ($userOk && $passOk)) {
            return null;
        }

        return new KantorkuProfile(
            id: (string) $cfg['id'],
            name: (string) $cfg['name'],
        );
    }
}
