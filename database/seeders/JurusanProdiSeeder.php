<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JurusanProdiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Teknik Mesin' => [
                'Program Studi Teknik Mesin',
                'Program Studi Teknik Konversi Energi',
                'Program Studi Teknologi Rekayasa Pengelasan dan Fabrikasi',
                'Program Studi Teknologi Rekayasa Energi Terbarukan',
                'Program Studi Teknologi Rekayasa Kimia Industri',
                'Program Studi Teknologi Rekayasa Manufaktur',
            ],
            'Teknik Sipil' => [
                'Program Studi Teknik Sipil',
                'Program Studi Teknik Perancangan Jalan dan Jembatan',
                'Program Studi Manajemen Rekayasa Konstruksi Gedung',
            ],
            'Teknik Elektro' => [
                'Program Studi Teknik Listrik',
                'Program Studi Teknik Elektronika',
                'Program Studi Teknik Telekomunikasi',
                'Program Studi Teknologi Rekayasa Jaringan Telekomunikasi',
                'Program Studi Teknologi Rekayasa Instalasi Listrik',
                'Program Studi Teknologi Rekayasa Otomasi',
            ],
            'Teknik Komputer dan Informatika' => [
                'Program Studi Teknik Komputer',
                'Program Studi Manajemen Informatika',
                'Program Studi Teknologi Rekayasa Perangkat Lunak',
                'Program Studi Teknologi Rekayasa Multimedia Grafis',
            ],
            'Akuntansi' => [
                'Program Studi Akuntansi',
                'Program Studi Keuangan dan Perbankan',
                'Program Studi Keuangan dan Perbankan Syariah',
                'Program Studi Akuntansi Keuangan Publik',
                'Magister Terapan Sistem Informasi Akuntansi', // Pascasarjana
            ],
            'Administrasi Niaga' => [
                'Program Studi Administrasi Bisnis',
                'Program Studi Manajemen Bisnis',
                'Program Studi Usaha Jasa Konvensi, Perjalanan Insentif dan Pameran',
                'Program Studi Bahasa Inggris',
            ],
        ];

        foreach ($data as $jurusanNama => $prodiList) {
            $jurusan = Jurusan::firstOrCreate(
                ['slug' => Str::slug($jurusanNama)],
                ['nama_jurusan' => $jurusanNama]
            );

            foreach ($prodiList as $prodiNama) {
                $jenjang = 'sarjana';
                if (str_contains($prodiNama, 'Magister')) {
                    $jenjang = 'pascasarjana';
                }
                Prodi::firstOrCreate(
                    ['slug' => Str::slug($prodiNama)],
                    [
                        'jurusan_id' => $jurusan->id,
                        'nama_prodi' => $prodiNama,
                        'jenjang' => $jenjang,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}