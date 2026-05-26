<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MySQL, kita perlu modify enum column
        Schema::table('pertanyaan', function (Blueprint $table) {
            // MySQL way to modify enum
            DB::statement("ALTER TABLE pertanyaan MODIFY COLUMN target_role ENUM('mahasiswa', 'dosen', 'both') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertanyaan', function (Blueprint $table) {
            DB::statement("ALTER TABLE pertanyaan MODIFY COLUMN target_role ENUM('mahasiswa', 'dosen') NOT NULL");
        });
    }
};