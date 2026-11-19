<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Blc;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Participant;
use App\Models\ParticipantCategory;
use App\Models\TrainingSession;
use App\Models\User;
use App\Models\VisitorCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    private int $seq = 0;

    private function actAs(UserRole $role, array $attributes = []): User
    {
        $this->seq++;

        $user = User::factory()->create(array_merge([
            'username' => $role->value.$this->seq,
            'email' => $role->value.$this->seq.'@example.com',
            'role' => $role,
            'is_active' => true,
        ], $attributes));

        Sanctum::actingAs($user);

        return $user;
    }

    private function makeUser(UserRole $role, array $attributes = []): User
    {
        $this->seq++;

        return User::factory()->create(array_merge([
            'username' => $role->value.$this->seq,
            'email' => $role->value.$this->seq.'@example.com',
            'role' => $role,
            'is_active' => true,
        ], $attributes));
    }

    // ---------------------------------------------------------------- akses

    public function test_tanpa_login_ditolak(): void
    {
        $this->getJson('/api/v1/kecamatans')->assertStatus(401);
        $this->getJson('/api/v1/blcs')->assertStatus(401);
    }

    public function test_user_biasa_boleh_baca_tapi_tidak_boleh_menulis(): void
    {
        $this->actAs(UserRole::Pengguna);

        $this->getJson('/api/v1/kecamatans')->assertOk();
        $this->postJson('/api/v1/kecamatans', ['name' => 'Baru'])->assertStatus(403);
        $this->postJson('/api/v1/blcs', ['name' => 'BLC X'])->assertStatus(403);
        $this->postJson('/api/v1/training-sessions', ['name' => 'S', 'start_time' => '08:00', 'end_time' => '09:00'])->assertStatus(403);
        $this->getJson('/api/v1/instructors')->assertStatus(403);
    }

    public function test_koordinator_boleh_lihat_instruktur_tapi_tidak_membuatnya(): void
    {
        $this->actAs(UserRole::Koordinator);

        $this->getJson('/api/v1/instructors')->assertOk();
        $this->postJson('/api/v1/instructors', [
            'name' => 'Budi', 'username' => 'budi', 'password' => 'rahasia123',
        ])->assertStatus(403);
    }

    // -------------------------------------------------------------- wilayah

    public function test_admin_mengelola_kecamatan_dan_kelurahan(): void
    {
        $this->actAs(UserRole::SuperAdmin);

        $id = $this->postJson('/api/v1/kecamatans', ['name' => 'Rungkut'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Rungkut')
            ->json('data.id');

        $this->postJson('/api/v1/kecamatans', ['name' => 'Rungkut'])->assertStatus(422);

        $this->postJson("/api/v1/kecamatans/{$id}/kelurahans", ['name' => 'Kali Rungkut'])->assertCreated();
        $this->postJson("/api/v1/kecamatans/{$id}/kelurahans", ['name' => 'Kali Rungkut'])->assertStatus(422);

        $this->getJson("/api/v1/kecamatans/{$id}/kelurahans")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->putJson("/api/v1/kecamatans/{$id}", ['name' => 'Rungkut Baru'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Rungkut Baru');
    }

    public function test_nama_kelurahan_boleh_sama_di_kecamatan_berbeda(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $a = Kecamatan::create(['name' => 'A']);
        $b = Kecamatan::create(['name' => 'B']);

        $this->postJson("/api/v1/kecamatans/{$a->id}/kelurahans", ['name' => 'Sama'])->assertCreated();
        $this->postJson("/api/v1/kecamatans/{$b->id}/kelurahans", ['name' => 'Sama'])->assertCreated();
    }

    public function test_kecamatan_dan_kelurahan_yang_dipakai_tidak_bisa_dihapus(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $kec = Kecamatan::create(['name' => 'Gubeng']);
        $kel = Kelurahan::create(['kecamatan_id' => $kec->id, 'name' => 'Airlangga']);

        // Kecamatan punya kelurahan.
        $this->deleteJson("/api/v1/kecamatans/{$kec->id}")->assertStatus(409);

        // Kelurahan dipakai BLC.
        Blc::create(['name' => 'BLC Gubeng', 'slug' => 'blc-gubeng', 'kecamatan_id' => $kec->id, 'kelurahan_id' => $kel->id]);
        $this->deleteJson("/api/v1/kelurahans/{$kel->id}")->assertStatus(409);
        $this->assertDatabaseHas('kelurahans', ['id' => $kel->id]);
    }

    public function test_kecamatan_kosong_bisa_dihapus(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $kec = Kecamatan::create(['name' => 'Kosong']);

        $this->deleteJson("/api/v1/kecamatans/{$kec->id}")->assertOk();
        $this->assertDatabaseMissing('kecamatans', ['id' => $kec->id]);
    }

    // ------------------------------------------------------------------ BLC

    public function test_membuat_blc_dengan_slug_otomatis_dan_unik(): void
    {
        $this->actAs(UserRole::SuperAdmin);

        $this->postJson('/api/v1/blcs', ['name' => 'BLC Pusat', 'region' => 'pusat'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'blc-pusat')
            ->assertJsonPath('data.region_label', 'Surabaya Pusat')
            ->assertJsonPath('data.is_active', true)
            ->assertJsonPath('data.show_on_landing', true);

        // Nama sama ditolak, tapi nama yang menghasilkan slug sama mendapat akhiran.
        $this->postJson('/api/v1/blcs', ['name' => 'BLC Pusat'])->assertStatus(422);
        $this->postJson('/api/v1/blcs', ['name' => 'BLC  Pusat!'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'blc-pusat-2');
    }

    public function test_validasi_blc(): void
    {
        $this->actAs(UserRole::SuperAdmin);

        $this->postJson('/api/v1/blcs', ['name' => 'X', 'region' => 'langit'])->assertStatus(422)->assertJsonValidationErrors('region');
        $this->postJson('/api/v1/blcs', ['name' => 'X', 'latitude' => 120])->assertStatus(422)->assertJsonValidationErrors('latitude');
        $this->postJson('/api/v1/blcs', ['name' => 'X', 'longitude' => -200])->assertStatus(422)->assertJsonValidationErrors('longitude');
        $this->postJson('/api/v1/blcs', [])->assertStatus(422)->assertJsonValidationErrors('name');
    }

    public function test_kelurahan_harus_sesuai_kecamatan(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $a = Kecamatan::create(['name' => 'A']);
        $b = Kecamatan::create(['name' => 'B']);
        $kelA = Kelurahan::create(['kecamatan_id' => $a->id, 'name' => 'KA']);

        // Kelurahan milik kecamatan lain.
        $this->postJson('/api/v1/blcs', ['name' => 'BLC 1', 'kecamatan_id' => $b->id, 'kelurahan_id' => $kelA->id])
            ->assertStatus(422)->assertJsonValidationErrors('kelurahan_id');

        // Kelurahan tanpa kecamatan.
        $this->postJson('/api/v1/blcs', ['name' => 'BLC 2', 'kelurahan_id' => $kelA->id])
            ->assertStatus(422)->assertJsonValidationErrors('kelurahan_id');

        // Sesuai.
        $id = $this->postJson('/api/v1/blcs', ['name' => 'BLC 3', 'kecamatan_id' => $a->id, 'kelurahan_id' => $kelA->id])
            ->assertCreated()->assertJsonPath('data.kelurahan.name', 'KA')->json('data.id');

        // Mengganti kecamatan tanpa mengganti kelurahan ditolak.
        $this->putJson("/api/v1/blcs/{$id}", ['kecamatan_id' => $b->id])->assertStatus(422);
    }

    public function test_ubah_sebagian_data_blc(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $blc = Blc::create(['name' => 'BLC Lama', 'slug' => 'blc-lama']);

        $this->patchJson("/api/v1/blcs/{$blc->id}", ['phone' => '031123'])
            ->assertOk()
            ->assertJsonPath('data.name', 'BLC Lama')
            ->assertJsonPath('data.slug', 'blc-lama')
            ->assertJsonPath('data.phone', '031123');
    }

    public function test_user_biasa_hanya_melihat_blc_aktif(): void
    {
        $aktif = Blc::create(['name' => 'Aktif', 'slug' => 'aktif', 'is_active' => true]);
        $mati = Blc::create(['name' => 'Mati', 'slug' => 'mati', 'is_active' => false]);

        $this->actAs(UserRole::Pengguna);
        $this->getJson('/api/v1/blcs')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Aktif');
        $this->getJson("/api/v1/blcs/{$aktif->id}")->assertOk();
        $this->getJson("/api/v1/blcs/{$mati->id}")->assertStatus(404);

        $this->actAs(UserRole::SuperAdmin);
        $this->getJson('/api/v1/blcs')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/blcs?is_active=0')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/blcs/{$mati->id}")->assertOk();
    }

    public function test_cari_dan_saring_blc(): void
    {
        Blc::create(['name' => 'BLC Rungkut', 'slug' => 'blc-rungkut', 'region' => 'timur']);
        Blc::create(['name' => 'BLC Tandes', 'slug' => 'blc-tandes', 'region' => 'barat']);

        $this->actAs(UserRole::SuperAdmin);

        $this->getJson('/api/v1/blcs?q=rung')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'blc-rungkut');
        $this->getJson('/api/v1/blcs?region=barat')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'blc-tandes');
    }

    public function test_blc_yang_punya_data_tidak_bisa_dihapus(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $dipakai = Blc::create(['name' => 'Dipakai', 'slug' => 'dipakai']);
        $dipakai->classes()->create(['name' => 'Kelas A']);
        $kosong = Blc::create(['name' => 'Kosong', 'slug' => 'kosong']);

        $this->deleteJson("/api/v1/blcs/{$dipakai->id}")->assertStatus(409);
        $this->assertDatabaseHas('blcs', ['id' => $dipakai->id]);

        $this->deleteJson("/api/v1/blcs/{$kosong->id}")->assertOk();
        $this->assertDatabaseMissing('blcs', ['id' => $kosong->id]);
    }

    // ------------------------------------------------------------ instruktur

    public function test_membuat_instruktur_kantorku_tanpa_password(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $blc = Blc::create(['name' => 'BLC A', 'slug' => 'blc-a']);

        $response = $this->postJson('/api/v1/instructors', [
            'name' => 'Siti',
            'username' => 'siti',
            'kantorku_id' => 'KTR-77',
            'blc_ids' => [$blc->id],
            'role' => 'admin', // harus diabaikan
        ])->assertCreated()
            ->assertJsonPath('data.kantorku_id', 'KTR-77')
            ->assertJsonPath('data.blcs.0.slug', 'blc-a');

        $user = User::find($response->json('data.id'));
        $this->assertSame(UserRole::Instruktur, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNotEmpty($user->password);
    }

    public function test_instruktur_lokal_wajib_punya_password(): void
    {
        $this->actAs(UserRole::SuperAdmin);

        $this->postJson('/api/v1/instructors', ['name' => 'Budi', 'username' => 'budi'])
            ->assertStatus(422)->assertJsonValidationErrors('password');

        $this->postJson('/api/v1/instructors', ['name' => 'Budi', 'username' => 'budi', 'password' => 'rahasia123'])
            ->assertCreated();

        // Username dan kantorku_id harus unik.
        $this->postJson('/api/v1/instructors', ['name' => 'Budi 2', 'username' => 'budi', 'password' => 'rahasia123'])
            ->assertStatus(422)->assertJsonValidationErrors('username');
    }

    public function test_daftar_instruktur_hanya_berisi_instruktur(): void
    {
        $this->makeUser(UserRole::Pengguna);
        $ins = $this->makeUser(UserRole::Instruktur, ['name' => 'Ani']);
        $this->actAs(UserRole::SuperAdmin);

        $this->getJson('/api/v1/instructors')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $ins->id);
        $this->getJson('/api/v1/instructors/'.$this->makeUser(UserRole::Pengguna)->id)->assertStatus(404);
    }

    public function test_menonaktifkan_instruktur_mencabut_token(): void
    {
        $ins = $this->makeUser(UserRole::Instruktur);
        $ins->createToken('web');
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->actAs(UserRole::SuperAdmin);

        $this->patchJson("/api/v1/instructors/{$ins->id}", ['is_active' => false])
            ->assertOk()->assertJsonPath('data.is_active', false);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_atur_instruktur_blc_tidak_menghapus_akun_lain(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $blc = Blc::create(['name' => 'BLC A', 'slug' => 'blc-a']);
        $a = $this->makeUser(UserRole::Instruktur);
        $b = $this->makeUser(UserRole::Instruktur);
        $pengunjung = $this->makeUser(UserRole::Pengguna);
        $blc->users()->attach([$a->id, $pengunjung->id]);

        $this->putJson("/api/v1/blcs/{$blc->id}/instructors", ['user_ids' => [$b->id]])
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $b->id);

        $this->assertDatabaseMissing('blc_user', ['blc_id' => $blc->id, 'user_id' => $a->id]);
        $this->assertDatabaseHas('blc_user', ['blc_id' => $blc->id, 'user_id' => $pengunjung->id]);

        $this->getJson("/api/v1/blcs/{$blc->id}/instructors")->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_hanya_instruktur_yang_bisa_ditugaskan_ke_blc(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $blc = Blc::create(['name' => 'BLC A', 'slug' => 'blc-a']);
        $userBiasa = $this->makeUser(UserRole::Pengguna);

        $this->putJson("/api/v1/blcs/{$blc->id}/instructors", ['user_ids' => [$userBiasa->id]])
            ->assertStatus(422)->assertJsonValidationErrors('user_ids.0');
    }

    // -------------------------------------------------------------- kategori

    public function test_kategori_peserta_dan_pengunjung(): void
    {
        $this->actAs(UserRole::SuperAdmin);

        $this->postJson('/api/v1/participant-categories', ['name' => 'Pelajar SMA', 'description' => 'Siswa SMA'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'pelajar-sma')
            ->assertJsonPath('data.description', 'Siswa SMA');

        $this->postJson('/api/v1/participant-categories', ['name' => 'Pelajar SMA'])->assertStatus(422);

        $this->postJson('/api/v1/visitor-categories', ['name' => 'Instansi'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'instansi')
            ->assertJsonMissingPath('data.description');
    }

    public function test_kategori_yang_dipakai_tidak_bisa_dihapus(): void
    {
        $this->actAs(UserRole::SuperAdmin);

        $kp = ParticipantCategory::create(['name' => 'Umum', 'slug' => 'umum']);
        Participant::create(['name' => 'Rina', 'participant_category_id' => $kp->id]);
        $this->deleteJson("/api/v1/participant-categories/{$kp->id}")->assertStatus(409);

        $kv = VisitorCategory::create(['name' => 'Sekolah', 'slug' => 'sekolah']);
        $blc = Blc::create(['name' => 'BLC A', 'slug' => 'blc-a']);
        $blc->guestbookEntries()->create(['visitor_category_id' => $kv->id, 'visited_at' => now()]);
        $this->deleteJson("/api/v1/visitor-categories/{$kv->id}")->assertStatus(409);

        $kosong = VisitorCategory::create(['name' => 'Kosong', 'slug' => 'kosong']);
        $this->deleteJson("/api/v1/visitor-categories/{$kosong->id}")->assertOk();
    }

    public function test_user_biasa_hanya_melihat_kategori_aktif(): void
    {
        ParticipantCategory::create(['name' => 'Aktif', 'slug' => 'aktif', 'is_active' => true]);
        $mati = ParticipantCategory::create(['name' => 'Mati', 'slug' => 'mati', 'is_active' => false]);

        $this->actAs(UserRole::Pengguna);
        $this->getJson('/api/v1/participant-categories')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/participant-categories/{$mati->id}")->assertStatus(404);
    }

    // ------------------------------------------------------------------ sesi

    public function test_sesi_pelatihan(): void
    {
        $this->actAs(UserRole::SuperAdmin);

        $id = $this->postJson('/api/v1/training-sessions', ['name' => 'Sesi Pagi', 'start_time' => '09:00', 'end_time' => '11:00'])
            ->assertCreated()
            ->assertJsonPath('data.start_time', '09:00')
            ->assertJsonPath('data.end_time', '11:00')
            ->json('data.id');

        $this->postJson('/api/v1/training-sessions', ['name' => 'Sesi Salah', 'start_time' => '11:00', 'end_time' => '09:00'])
            ->assertStatus(422)->assertJsonValidationErrors('end_time');
        $this->postJson('/api/v1/training-sessions', ['name' => 'Sesi Aneh', 'start_time' => '9 pagi', 'end_time' => '11:00'])
            ->assertStatus(422)->assertJsonValidationErrors('start_time');

        // Mengubah satu jam dibandingkan dengan jam yang tersimpan.
        $this->patchJson("/api/v1/training-sessions/{$id}", ['end_time' => '08:00'])->assertStatus(422);
        $this->patchJson("/api/v1/training-sessions/{$id}", ['end_time' => '12:00'])
            ->assertOk()->assertJsonPath('data.end_time', '12:00');
    }

    public function test_sesi_yang_dipakai_kelas_tidak_bisa_dihapus(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $sesi = TrainingSession::create(['name' => 'Sesi 1', 'start_time' => '08:00', 'end_time' => '10:00']);
        $blc = Blc::create(['name' => 'BLC A', 'slug' => 'blc-a']);
        $blc->classes()->create(['name' => 'Kelas A', 'training_session_id' => $sesi->id]);

        $this->deleteJson("/api/v1/training-sessions/{$sesi->id}")->assertStatus(409);
    }

    public function test_daftar_sesi_diurutkan_menurut_jam_mulai(): void
    {
        TrainingSession::create(['name' => 'Siang', 'start_time' => '13:00', 'end_time' => '15:00']);
        TrainingSession::create(['name' => 'Pagi', 'start_time' => '08:00', 'end_time' => '10:00']);

        $this->actAs(UserRole::Pengguna);
        $this->getJson('/api/v1/training-sessions')->assertOk()
            ->assertJsonPath('data.0.name', 'Pagi')
            ->assertJsonPath('data.1.name', 'Siang');
    }

    // ------------------------------------------------------------ koordinator
    public function test_admin_mengelola_koordinator_dan_blc(): void
    {
        $this->actAs(UserRole::SuperAdmin);
        $blc = Blc::create(['name' => 'BLC A', 'slug' => 'blc-a']);
        $this->postJson('/api/v1/koordinator', [
            'name' => 'Siti',
            'email' => 'siti@example.com',
            'phone' => '081234567890',
            'kantorku_id' => 'KTR-77',
            'password' => 'rahasia123',
            'lokasi_blc_ids' => [$blc->id],
        ])->assertCreated()
            ->assertJsonPath('data.kantorku_id', 'KTR-77')
            ->assertJsonPath('data.blcs.0.id', $blc->id)

            ->assertJsonPath('data.blcs.0.name', 'BLC A');
    }
}
