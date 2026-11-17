<?php

namespace App\Services\Auth;

use App\Exceptions\KantorkuUnavailable;

/**
 * Adapter ke API Kantorku asli. Belum diisi karena dokumentasi API (URL, format
 * request, dan format respons) belum ada. Saat sudah ada, cukup isi method ini
 * dengan Http::timeout(...)->post(config('kantorku.login_url'), [...]) lalu
 * petakan respons menjadi KantorkuProfile. Bagian lain tidak perlu diubah.
 */
class HttpKantorkuClient implements KantorkuClient
{
    public function authenticate(string $username, string $password): ?KantorkuProfile
    {
        throw new KantorkuUnavailable('Adapter HTTP Kantorku belum dikonfigurasi.');
    }
}
