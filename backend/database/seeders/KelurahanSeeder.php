<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Database\Seeder;

class KelurahanSeeder extends Seeder
{
    /**
     * Mengisi kelurahan dari database/seeders/data/kelurahan.csv (kolom: kecamatan,kelurahan).
     * Daftar kelurahan sengaja tidak ditulis dari ingatan: isi file itu dari data resmi atau
     * dari data Oracle lama, supaya sama persis dengan yang dipakai BLC. Baris yang kecamatannya
     * tidak dikenal dilewati dan dilaporkan.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/kelurahan.csv');

        if (! is_file($path)) {
            $this->command?->warn('database/seeders/data/kelurahan.csv belum ada, kelurahan dilewati.');

            return;
        }

        $kecamatans = Kecamatan::query()->pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [mb_strtolower($name) => $id]);
        $handle = fopen($path, 'r');
        $header = true;
        $created = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if ($header) {
                $header = false;

                continue;
            }

            [$kecamatan, $kelurahan] = array_pad(array_map('trim', $row), 2, '');

            if ($kecamatan === '' || $kelurahan === '') {
                continue;
            }

            $kecamatanId = $kecamatans[mb_strtolower($kecamatan)] ?? null;

            if ($kecamatanId === null) {
                $this->command?->warn("Kecamatan '{$kecamatan}' tidak dikenal, '{$kelurahan}' dilewati.");

                continue;
            }

            $model = Kelurahan::firstOrCreate(['kecamatan_id' => $kecamatanId, 'name' => $kelurahan]);
            $created += $model->wasRecentlyCreated ? 1 : 0;
        }

        fclose($handle);

        $this->command?->info("Kelurahan ditambahkan: {$created}");
    }
}
