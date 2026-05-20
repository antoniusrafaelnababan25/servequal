<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['setting_key' => 'kuesioner_status', 'setting_value' => 'open'],
            ['setting_key' => 'app_name', 'setting_value' => 'Sistem Monitoring SERVQUAL POLMED'],
            ['setting_key' => 'app_version', 'setting_value' => '1.0.0'],
            ['setting_key' => 'target_jurusan', 'setting_value' => 'teknologi_informasi'],
            ['setting_key' => 'tujuan_kuesioner', 'setting_value' => 'Mengukur kepuasan layanan akademik dengan metode SERVQUAL'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                ['setting_value' => $setting['setting_value']]
            );
        }
    }
}