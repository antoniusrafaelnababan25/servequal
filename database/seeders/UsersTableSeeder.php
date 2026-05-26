<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@polmed.ac.id'],
            [
                'username' => 'superadmin',
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@polmed.ac.id'],
            [
                'username' => 'admin',
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Dosen (Unique list from all evaluation data)
        $dosenList = [
            [
                'username' => 'wiwin.banjarnahor',
                'name' => 'Wiwin Sry Adinda Banjarnahor, S.Kom., M.Sc.',
                'email' => 'wiwin.banjarnahor@polmed.ac.id',
                'nidn' => '0120078505',
            ],
            [
                'username' => 'benny.nasution',
                'name' => 'Dr. Benny Benyamin Nasution, Dipl. Ing., M. Eng',
                'email' => 'benny.nasution@polmed.ac.id',
                'nidn' => '0009086807',
            ],
            [
                'username' => 'yunita.siregar',
                'name' => 'Yunita Sari Siregar, S.T.,M.Kom',
                'email' => 'yunita.siregar@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'marliana.sari',
                'name' => 'Marliana Sari, S.T., M.MSI',
                'email' => 'marliana.sari@polmed.ac.id',
                'nidn' => '0327037701',
            ],
            [
                'username' => 'junus.sinuraya',
                'name' => 'Junus Sinuraya, S.T., M. Kom.',
                'email' => 'junus.sinuraya@polmed.ac.id',
                'nidn' => '0110068103',
            ],
            [
                'username' => 'orli.tumanggor',
                'name' => 'Orli Binta Tumanggor, S.Pd., M.Hum.',
                'email' => 'orli.tumanggor@polmed.ac.id',
                'nidn' => '0014069204',
            ],
            [
                'username' => 'suci.khairani',
                'name' => 'Suci Khairani, S.Pd., M.Si',
                'email' => 'suci.khairani@polmed.ac.id',
                'nidn' => '199208042019032029',
            ],
            [
                'username' => 'azanuddin',
                'name' => 'Azanuddin, S.Kom., M.Kom.',
                'email' => 'azanuddin@polmed.ac.id',
                'nidn' => '2305112041',
            ],
            [
                'username' => 'weno.syechu',
                'name' => 'Weno Syechu, M.Kom',
                'email' => 'weno.syechu@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'amir.saleh',
                'name' => 'Amir Saleh, S.Pd.,M.Kom',
                'email' => 'amir.saleh@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'achmad.yani',
                'name' => 'Achmad Yani, S.T., M.Kom.',
                'email' => 'achmad.yani@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'gunawan',
                'name' => 'Gunawan, S.T., M.Kom',
                'email' => 'gunawan@polmed.ac.id',
                'nidn' => '19750604200003002',
            ],
            [
                'username' => 'ajulio.sembiring',
                'name' => 'Ajulio Padly Sembiring, S.T., M.Kom.',
                'email' => 'ajulio.sembiring@polmed.ac.id',
                'nidn' => '0016079203',
            ],
            [
                'username' => 'muhammad.riki',
                'name' => 'Muhammad Riki Atsauri, S.T.,M.Kom',
                'email' => 'muhammad.riki@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'mardiana',
                'name' => 'Mardiana, S.T., M.Kom',
                'email' => 'mardiana@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'kadri.yusuf',
                'name' => 'Kadri Yusuf, S.T., M.Kom.',
                'email' => 'kadri.yusuf@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'winda.syafitri',
                'name' => 'Winda Syafitri, S.Pd., M.Pd.',
                'email' => 'winda.syafitri@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'aprilza.aswani',
                'name' => 'Aprilza Aswani, SPd., M.A.',
                'email' => 'aprilza.aswani@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'purwa.putra',
                'name' => 'Purwa Hasan Putra, M.Kom',
                'email' => 'purwa.putra@polmed.ac.id',
                'nidn' => null,
            ],
            [
                'username' => 'hermansyah.sembiring',
                'name' => 'Hermansyah Sembiring',
                'email' => 'hermansyah.sembiring@polmed.ac.id',
                'nidn' => null,
            ],
        ];

        foreach ($dosenList as $dosen) {
            User::updateOrCreate(
                ['email' => $dosen['email']],
                [
                    'username' => $dosen['username'],
                    'name' => $dosen['name'],
                    'password' => Hash::make('dosen123'),
                    'role' => 'dosen',
                    'nidn' => $dosen['nidn'],
                    'is_active' => true,
                ]
            );
        }

        // Mahasiswa (contoh)
        $mahasiswaList = [
            [
                'nim' => '202001001',
                'name' => 'Andi Saputra',
                'email' => 'andi.saputra@students.polmed.ac.id',
                'kelas' => 'TI-1A',
                'tanggal_lahir' => '2002-05-15',
            ],
            [
                'nim' => '202001002',
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@students.polmed.ac.id',
                'kelas' => 'TM-1B',
                'tanggal_lahir' => '2001-10-20',
            ],
        ];

        foreach ($mahasiswaList as $mhs) {
            User::updateOrCreate(
                ['nim' => $mhs['nim']],
                [
                    'username' => $mhs['nim'],
                    'name' => $mhs['name'],
                    'email' => $mhs['email'],
                    'password' => Hash::make('mahasiswa123'),
                    'role' => 'mahasiswa',
                    'nim' => $mhs['nim'],
                    'kelas' => $mhs['kelas'],
                    'tanggal_lahir' => $mhs['tanggal_lahir'],
                    'is_active' => true,
                ]
            );
        }
    }
}