<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('penilaian_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->string('dosen_nama', 100);
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            $table->string('mahasiswa_nama', 100);
            $table->string('mahasiswa_nim', 20);
            $table->string('kelas', 20)->nullable();
            $table->string('mata_kuliah', 100)->nullable();
            $table->json('nilai');
            $table->decimal('rata_rata', 3, 2)->nullable();
            $table->timestamps();

            $table->index('dosen_id');
            $table->index('mahasiswa_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penilaian_dosen');
    }
};