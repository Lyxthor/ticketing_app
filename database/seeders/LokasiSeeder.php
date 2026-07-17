<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ['Stadion  Utama', 'Galeri Seni Kota', 'Taman Kota'];
        foreach ($data as $lokasi) {
            Lokasi::create(['nama_lokasi'=>$lokasi]);
        }
    }
}
