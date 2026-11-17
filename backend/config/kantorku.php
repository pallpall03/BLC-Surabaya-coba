<?php

return [

    /*
    | Driver login Kantorku (SSO):
    |  - fake : akun percobaan dari .env, HANYA jalan di environment local/testing
    |  - http : koneksi ke API Kantorku asli (belum diisi, menunggu dokumentasi API)
    */
    'driver' => env('KANTORKU_DRIVER', 'fake'),

    'timeout' => (int) env('KANTORKU_TIMEOUT', 10),
    'login_url' => env('KANTORKU_LOGIN_URL'),

    'fake' => [
        'username' => env('KANTORKU_FAKE_USERNAME'),
        'password' => env('KANTORKU_FAKE_PASSWORD'),
        'id' => env('KANTORKU_FAKE_ID', 'KTR-FAKE-001'),
        'name' => env('KANTORKU_FAKE_NAME', 'Instruktur Percobaan'),
    ],

];
