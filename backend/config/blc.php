<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    | captcha_after_failures : captcha mulai wajib setelah sekian kali salah password
    | max_failures           : batas gagal sebelum dikunci sementara (per login + IP)
    | decay_minutes          : lama penguncian dan masa berlaku hitungan gagal
    | token_ttl_minutes      : masa berlaku token login (default 8 jam)
    */
    'login' => [
        'captcha_after_failures' => (int) env('LOGIN_CAPTCHA_AFTER', 1),
        'max_failures' => (int) env('LOGIN_MAX_FAILURES', 5),
        'decay_minutes' => (int) env('LOGIN_DECAY_MINUTES', 15),
        'token_ttl_minutes' => (int) env('AUTH_TOKEN_TTL_MINUTES', 480),
    ],

    'captcha' => [
        'ttl_seconds' => (int) env('CAPTCHA_TTL_SECONDS', 300),
    ],

];
