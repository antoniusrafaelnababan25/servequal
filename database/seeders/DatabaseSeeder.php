<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JurusanProdiSeeder::class,
            UsersTableSeeder::class,
                // UpdateUserProdiIdSeeder::class, // Uncomment jika perlu migrasi data user lama
            PertanyaanTableSeeder::class,
            KuesionerPeriodeSeeder::class,
            SystemSettingsTableSeeder::class,
        ]);
    }
}