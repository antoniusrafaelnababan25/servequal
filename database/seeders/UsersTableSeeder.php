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

        // Dosen (contoh 2 dosen, bisa ditambah nanti sesuai kebutuhan)
        $dosenList = [
            [
                'username' => 'dosen1',
                'name' => 'Dr. Ahmad Fauzi, M.Kom',
                'email' => 'ahmad.fauzi@polmed.ac.id',
                'nidn' => '0123456789',
                'jurusan' => 'Teknik Informatika',
            ],
            [
                'username' => 'dosen2',
                'name' => 'Dr. Siti Nurhaliza, M.Sc',
                'email' => 'siti.nurhaliza@polmed.ac.id',
                'nidn' => '9876543210',
                'jurusan' => 'Teknik Mesin',
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
                    'jurusan' => $dosen['jurusan'],
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
                'jurusan' => 'Teknik Informatika',
                'kelas' => 'TI-1A',
                'tanggal_lahir' => '2002-05-15',
            ],
            [
                'nim' => '202001002',
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@students.polmed.ac.id',
                'jurusan' => 'Teknik Mesin',
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
                    'jurusan' => $mhs['jurusan'],
                    'kelas' => $mhs['kelas'],
                    'tanggal_lahir' => $mhs['tanggal_lahir'],
                    'is_active' => true,
                ]
            );
        }
    }
}