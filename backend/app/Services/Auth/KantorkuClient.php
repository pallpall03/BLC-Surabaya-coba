<?php

namespace App\Services\Auth;

use App\Exceptions\KantorkuUnavailable;

interface KantorkuClient
{
    /**
     * Memeriksa username dan password ke Kantorku.
     * Mengembalikan profil kalau berhasil, null kalau kredensial salah.
     *
     * @throws KantorkuUnavailable bila layanan Kantorku tidak bisa dihubungi atau belum dikonfigurasi
     */
    public function authenticate(string $username, string $password): ?KantorkuProfile;
}
