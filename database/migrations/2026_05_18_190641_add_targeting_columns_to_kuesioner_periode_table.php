<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('kuesioner_periode', function (Blueprint $table) {
            $table->enum('target_role', ['mahasiswa', 'dosen', 'both'])->default('both')->after('target_jurusan');
            $table->foreignId('target_prodi_id')->nullable()->after('target_role')->constrained('prodi')->onDelete('set null');
            $table->enum('target_jenjang', ['sarjana', 'pascasarjana', 'internasional', 'all'])->default('all')->after('target_prodi_id');
        });
    }

    public function down()
    {
        Schema::table('kuesioner_periode', function (Blueprint $table) {
            $table->dropColumn(['target_role', 'target_prodi_id', 'target_jenjang']);
        });
    }
};