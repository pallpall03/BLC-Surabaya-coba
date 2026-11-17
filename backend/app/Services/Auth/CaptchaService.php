<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Captcha hitung sederhana tanpa layanan eksternal. Jawaban hanya disimpan di server
 * (cache), berlaku sekali pakai, dan kedaluwarsa otomatis. Kalau nanti butuh yang lebih
 * kuat (Turnstile, reCAPTCHA, atau captcha gambar), ganti isi class ini saja.
 */
class CaptchaService
{
    private const PREFIX = 'captcha:';

    /** @return array{id: string, question: string} */
    public function make(): array
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);

        [$question, $answer] = match (random_int(0, 2)) {
            0 => ["Berapa {$a} + {$b} ?", $a + $b],
            1 => ['Berapa '.max($a, $b).' - '.min($a, $b).' ?', max($a, $b) - min($a, $b)],
            default => ["Berapa {$a} x {$b} ?", $a * $b],
        };

        $id = Str::random(32);

        Cache::put(self::PREFIX.$id, $answer, now()->addSeconds((int) config('blc.captcha.ttl_seconds', 300)));

        return ['id' => $id, 'question' => $question];
    }

    public function verify(?string $id, mixed $answer): bool
    {
        if ($id === null || $id === '' || $answer === null || $answer === '') {
            return false;
        }

        // pull = ambil lalu hapus, jadi satu captcha hanya bisa dicoba sekali.
        $expected = Cache::pull(self::PREFIX.$id);

        if ($expected === null) {
            return false;
        }

        $given = filter_var(is_string($answer) ? trim($answer) : $answer, FILTER_VALIDATE_INT);

        return $given !== false && $given === (int) $expected;
    }
}
