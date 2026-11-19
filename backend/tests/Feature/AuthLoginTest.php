<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'username' => 'budi',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
            'role' => UserRole::Pengguna,
            'is_active' => true,
        ], $attributes));
    }

    private function login(array $payload): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/api/v1/auth/login', $payload);
    }

    /** Jawaban captcha dibaca dari cache (hanya mungkin di test, klien tidak pernah menerimanya). */
    private function answerFor(string $captchaId): string
    {
        return (string) Cache::get('captcha:'.$captchaId);
    }

    public function test_login_berhasil_dengan_username(): void
    {
        $user = $this->makeUser();

        $response = $this->login(['login' => 'budi', 'password' => 'rahasia123']);

        $response->assertOk()
            ->assertJsonPath('user.role', 'pengguna')
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonStructure(['token', 'expires_in', 'user' => ['id', 'name', 'username', 'email', 'role', 'role_label']]);

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_login_berhasil_dengan_email_tanpa_peduli_huruf_besar(): void
    {
        $this->makeUser();

        $this->login(['login' => 'BUDI@Example.com', 'password' => 'rahasia123'])->assertOk();
    }

    public function test_password_salah_memunculkan_captcha_dan_pesan_umum(): void
    {
        $this->makeUser();

        $response = $this->login(['login' => 'budi', 'password' => 'salah']);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Username atau password salah.')
            ->assertJsonPath('captcha_required', true)
            ->assertJsonStructure(['captcha' => ['id', 'question']]);
    }

    public function test_user_tidak_dikenal_dibalas_sama_dengan_password_salah(): void
    {
        $this->makeUser();

        $unknown = $this->login(['login' => 'tidak-ada', 'password' => 'apa-saja']);
        $wrong = $this->login(['login' => 'budi', 'password' => 'salah']);

        $unknown->assertStatus(401)->assertJsonPath('message', 'Username atau password salah.');
        $this->assertSame($wrong->json('message'), $unknown->json('message'));
    }

    public function test_captcha_tidak_diminta_pada_percobaan_pertama(): void
    {
        $this->makeUser();

        $this->login(['login' => 'budi', 'password' => 'rahasia123'])
            ->assertOk()
            ->assertJsonMissingPath('captcha');
    }

    public function test_setelah_gagal_login_tanpa_captcha_ditolak(): void
    {
        $this->makeUser();
        $this->login(['login' => 'budi', 'password' => 'salah']);

        $this->login(['login' => 'budi', 'password' => 'rahasia123'])
            ->assertStatus(422)
            ->assertJsonPath('captcha_required', true)
            ->assertJsonStructure(['captcha' => ['id', 'question']]);
    }

    public function test_setelah_gagal_login_berhasil_dengan_captcha_benar(): void
    {
        $this->makeUser();
        $failed = $this->login(['login' => 'budi', 'password' => 'salah']);
        $captchaId = $failed->json('captcha.id');

        $this->login([
            'login' => 'budi',
            'password' => 'rahasia123',
            'captcha_id' => $captchaId,
            'captcha_answer' => $this->answerFor($captchaId),
        ])->assertOk();
    }

    public function test_captcha_salah_ditolak_dan_hanya_bisa_dicoba_sekali(): void
    {
        $this->makeUser();
        $captchaId = $this->login(['login' => 'budi', 'password' => 'salah'])->json('captcha.id');
        $right = $this->answerFor($captchaId);

        $this->login([
            'login' => 'budi',
            'password' => 'rahasia123',
            'captcha_id' => $captchaId,
            'captcha_answer' => '999',
        ])->assertStatus(422)->assertJsonPath('captcha_required', true);

        // Captcha yang sama sudah terpakai, jawaban benar pun tidak berlaku lagi.
        $this->login([
            'login' => 'budi',
            'password' => 'rahasia123',
            'captcha_id' => $captchaId,
            'captcha_answer' => $right,
        ])->assertStatus(422);
    }

    public function test_akun_dikunci_sementara_setelah_terlalu_banyak_gagal(): void
    {
        $this->makeUser();
        config(['blc.login.max_failures' => 3]);

        $captchaId = null;

        for ($i = 0; $i < 3; $i++) {
            $payload = ['login' => 'budi', 'password' => 'salah'];

            if ($captchaId !== null) {
                $payload['captcha_id'] = $captchaId;
                $payload['captcha_answer'] = $this->answerFor($captchaId);
            }

            $captchaId = $this->login($payload)->json('captcha.id');
        }

        $this->login([
            'login' => 'budi',
            'password' => 'rahasia123',
            'captcha_id' => $captchaId,
            'captcha_answer' => $this->answerFor($captchaId),
        ])->assertStatus(429)->assertJsonStructure(['retry_after']);
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        $this->makeUser(['is_active' => false]);

        $this->login(['login' => 'budi', 'password' => 'rahasia123'])->assertStatus(403);
    }

    public function test_instruktur_kantorku_bisa_login_lewat_pintu_yang_sama(): void
    {
        config([
            'kantorku.driver' => 'fake',
            'kantorku.fake.username' => 'instruktur1',
            'kantorku.fake.password' => 'sandi-kantorku',
            'kantorku.fake.id' => 'KTR-1',
        ]);

        $this->makeUser([
            'username' => 'instruktur1',
            'email' => null,
            'role' => UserRole::Instruktur,
            'kantorku_id' => 'KTR-1',
        ]);

        $this->login(['login' => 'instruktur1', 'password' => 'sandi-kantorku'])
            ->assertOk()
            ->assertJsonPath('user.role', 'instruktur');
    }

    public function test_instruktur_kantorku_tidak_bisa_login_dengan_password_lokal(): void
    {
        config([
            'kantorku.fake.username' => 'instruktur1',
            'kantorku.fake.password' => 'sandi-kantorku',
            'kantorku.fake.id' => 'KTR-1',
        ]);

        $this->makeUser([
            'username' => 'instruktur1',
            'role' => UserRole::Instruktur,
            'kantorku_id' => 'KTR-1',
            'password' => 'password-lokal',
        ]);

        $this->login(['login' => 'instruktur1', 'password' => 'password-lokal'])->assertStatus(401);
    }

    public function test_me_butuh_login_dan_mengembalikan_data_user(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);

        $this->makeUser();
        $token = $this->login(['login' => 'budi', 'password' => 'rahasia123'])->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('user.username', 'budi');
    }

    public function test_logout_menghapus_token(): void
    {
        $this->makeUser();
        $token = $this->login(['login' => 'budi', 'password' => 'rahasia123'])->json('token');
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_route_per_role(): void
    {
        $this->getJson('/api/v1/admin/ping')->assertStatus(401);

        Sanctum::actingAs($this->makeUser(['username' => 'user1', 'email' => 'p@example.com']));
        $this->getJson('/api/v1/admin/ping')->assertStatus(403);

        Sanctum::actingAs($this->makeUser(['username' => 'admin1', 'email' => 'a@example.com', 'role' => UserRole::SuperAdmin]));
        $this->getJson('/api/v1/admin/ping')->assertOk();
    }

    public function test_akun_yang_dinonaktifkan_langsung_kehilangan_akses(): void
    {
        Sanctum::actingAs($this->makeUser(['is_active' => false, 'role' => UserRole::SuperAdmin]));

        $this->getJson('/api/v1/admin/ping')->assertStatus(403);
    }
}
