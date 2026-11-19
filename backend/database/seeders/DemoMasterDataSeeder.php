<?php

namespace Database\Seeders;

use App\Models\Blc;
use App\Models\ParticipantCategory;
use App\Models\TrainingSession;
use App\Models\VisitorCategory;
use Illuminate\Database\Seeder;

/**
 * Data CONTOH untuk pengembangan lokal saja (dipanggil DatabaseSeeder hanya di environment local).
 * Semua nama sengaja berawalan "Contoh" supaya tidak tertukar dengan data asli.
 * Ganti dengan data asli BLC (dari Oracle lama atau input admin) sebelum dipakai sungguhan.
 */
class DemoMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Contoh BLC Pusat' => 'pusat', 'Contoh BLC Timur' => 'timur'] as $name => $region) {
            Blc::firstOrCreate(
                ['slug' => str($name)->slug()->toString()],
                ['name' => $name, 'region' => $region, 'show_on_landing' => true, 'is_active' => true],
            );
        }

        foreach (['Contoh Pelajar', 'Contoh Umum'] as $name) {
            ParticipantCategory::firstOrCreate(['slug' => str($name)->slug()->toString()], ['name' => $name]);
        }

        foreach (['Contoh Sekolah', 'Contoh Komunitas'] as $name) {
            VisitorCategory::firstOrCreate(['slug' => str($name)->slug()->toString()], ['name' => $name]);
        }

        foreach ([['Contoh Sesi Pagi', '09:00', '11:00'], ['Contoh Sesi Siang', '13:00', '15:00']] as [$name, $start, $end]) {
            TrainingSession::firstOrCreate(['name' => $name], ['start_time' => $start, 'end_time' => $end]);
        }
    }
}
