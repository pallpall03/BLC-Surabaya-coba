<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    /** 31 kecamatan di Kota Surabaya. Cocokkan dengan data Oracle lama saat migrasi. */
    public function run(): void
    {
        $names = [
            'Asemrowo', 'Benowo', 'Bubutan', 'Bulak', 'Dukuh Pakis', 'Gayungan', 'Genteng',
            'Gubeng', 'Gunung Anyar', 'Jambangan', 'Karang Pilang', 'Kenjeran', 'Krembangan',
            'Lakarsantri', 'Mulyorejo', 'Pabean Cantian', 'Pakal', 'Rungkut', 'Sambikerep',
            'Sawahan', 'Semampir', 'Simokerto', 'Sukolilo', 'Sukomanunggal', 'Tambaksari',
            'Tandes', 'Tegalsari', 'Tenggilis Mejoyo', 'Wiyung', 'Wonocolo', 'Wonokromo',
        ];

        foreach ($names as $name) {
            Kecamatan::firstOrCreate(['name' => $name]);
        }
    }
}
