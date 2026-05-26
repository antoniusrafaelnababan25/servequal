<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pertanyaan', function (Blueprint $table) {
            $table->enum('tipe_penilaian', ['penilaian_dosen', 'penilaian_fasilitas'])->default('penilaian_dosen')->after('target_role');
            $table->enum('kategori_fasilitas', ['umum', 'peralatan', 'ruangan', 'akses', 'infrastruktur'])->nullable()->after('tipe_penilaian');
        });
    }

    public function down()
    {
        Schema::table('pertanyaan', function (Blueprint $table) {
            $table->dropColumn(['tipe_penilaian', 'kategori_fasilitas']);
        });
    }
};