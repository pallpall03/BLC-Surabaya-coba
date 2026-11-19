<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\KantorkuUnavailable;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterPenggunaRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\Auth\CaptchaService;
use App\Services\Auth\CredentialAuthenticator;
use App\Services\Auth\LoginAttempts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly CredentialAuthenticator $authenticator,
        private readonly CaptchaService $captcha,
        private readonly LoginAttempts $attempts,
    ) {
    }

    public function captcha(): JsonResponse
    {
        return response()->json($this->captcha->make());
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->validated('login');
        $ip = (string) $request->ip();

        if ($this->attempts->isLockedOut($login, $ip)) {
            $retry = $this->attempts->retryAfter($login, $ip);

            return response()->json([
                'message' => 'Terlalu banyak percobaan gagal. Coba lagi beberapa saat lagi.',
                'retry_after' => $retry,
                'captcha_required' => true,
            ], 429)->header('Retry-After', (string) $retry);
        }

        if ($this->attempts->captchaRequired($login, $ip)) {
            $id = $request->validated('captcha_id');
            $answer = $request->validated('captcha_answer');

            if ($id === null || $answer === null) {
                return $this->failure('Selesaikan captcha terlebih dahulu.', 422, $login, $ip);
            }

            if (! $this->captcha->verify($id, $answer)) {
                $this->attempts->hit($login, $ip);

                return $this->failure('Jawaban captcha salah atau sudah kedaluwarsa.', 422, $login, $ip);
            }
        }

        try {
            $user = $this->authenticator->attempt($login, $request->validated('password'));
        } catch (KantorkuUnavailable) {
            return response()->json(['message' => 'Layanan Kantorku sedang tidak tersedia. Coba lagi nanti.'], 503);
        }

        if ($user === null) {
            $this->attempts->hit($login, $ip);

            return $this->failure('Username atau password salah.', 401, $login, $ip);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Akun dinonaktifkan. Hubungi admin BLC.'], 403);
        }

        $this->attempts->clear($login, $ip);
        $user->forceFill(['last_login_at' => now()])->save();

        $ttl = (int) config('blc.login.token_ttl_minutes', 480);
        $token = $user->createToken('web', ['*'], now()->addMinutes($ttl))->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttl * 60,
            'user' => UserResource::make($user->loadMissing('blcs'))->resolve($request),
        ]);
    }

    public function register(RegisterPenggunaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => UserRole::Pengguna,
            'is_active' => true,
        ]);

        $ttl = (int) config('blc.login.token_ttl_minutes', 480);
        $token = $user->createToken('web', ['*'], now()->addMinutes($ttl))->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttl * 60,
            'user' => UserResource::make($user)->resolve($request),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => UserResource::make($request->user()->loadMissing('blcs'))->resolve($request),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    /** Respons gagal; menyertakan captcha baru bila captcha sudah wajib. */
    private function failure(string $message, int $status, string $login, string $ip): JsonResponse
    {
        $required = $this->attempts->captchaRequired($login, $ip);

        $payload = ['message' => $message, 'captcha_required' => $required];

        if ($required) {
            $payload['captcha'] = $this->captcha->make();
        }

        return response()->json($payload, $status);
    }
}
