<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            KecamatanSeeder::class,
            KelurahanSeeder::class,
            AdminSeeder::class,
        ]);

        // Data contoh hanya untuk lokal, tidak pernah ikut ke produksi.
        if (app()->environment('local')) {
            $this->call(DemoMasterDataSeeder::class);
        }
    }
}
