<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Cache;

/**
 * Menghitung percobaan login gagal per kombinasi (login + IP).
 * Memicu captcha setelah gagal pertama, dan mengunci sementara setelah batas tercapai.
 */
class LoginAttempts
{
    private function key(string $login, string $ip): string
    {
        return 'login-fail:'.sha1(mb_strtolower(trim($login)).'|'.$ip);
    }

    public function failures(string $login, string $ip): int
    {
        return (int) Cache::get($this->key($login, $ip), 0);
    }

    public function hit(string $login, string $ip): void
    {
        $key = $this->key($login, $ip);
        $decay = (int) config('blc.login.decay_minutes', 15);

        Cache::add($key, 0, now()->addMinutes($decay));
        $count = (int) Cache::increment($key);

        if ($count >= (int) config('blc.login.max_failures', 5)) {
            Cache::put($key.':lock', now()->addMinutes($decay)->getTimestamp(), now()->addMinutes($decay));
        }
    }

    public function captchaRequired(string $login, string $ip): bool
    {
        return $this->failures($login, $ip) >= (int) config('blc.login.captcha_after_failures', 1);
    }

    public function retryAfter(string $login, string $ip): int
    {
        $until = Cache::get($this->key($login, $ip).':lock');

        return $until ? max(0, (int) $until - now()->getTimestamp()) : 0;
    }

    public function isLockedOut(string $login, string $ip): bool
    {
        return $this->retryAfter($login, $ip) > 0;
    }

    public function clear(string $login, string $ip): void
    {
        $key = $this->key($login, $ip);

        Cache::forget($key);
        Cache::forget($key.':lock');
    }
}
