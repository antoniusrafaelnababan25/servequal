<?php

namespace Database\Seeders;

use App\Models\KuesionerPeriode;
use Illuminate\Database\Seeder;

class KuesionerPeriodeSeeder extends Seeder
{
    public function run(): void
    {
        // Periode yang sudah lewat (tutup)
        KuesionerPeriode::updateOrCreate(
            ['nama_periode' => 'Ganjil 2024/2025'],
            [
                'tanggal_mulai' => '2024-09-01',
                'tanggal_selesai' => '2024-12-20',
                'status' => 'tutup',
                'target_jurusan' => 'teknologi_informasi',
                'tujuan' => 'Evaluasi layanan akademik semester ganjil 2024/2025',
                'is_active' => false,
            ]
        );

        // Periode aktif (sedang berjalan)
        KuesionerPeriode::updateOrCreate(
            ['nama_periode' => 'Genap 2024/2025'],
            [
                'tanggal_mulai' => '2025-02-01',
                'tanggal_selesai' => '2025-06-15',
                'status' => 'aktif',
                'target_jurusan' => 'teknologi_informasi',
                'tujuan' => 'Evaluasi layanan akademik semester genap 2024/2025',
                'is_active' => true,
            ]
        );
    }
}