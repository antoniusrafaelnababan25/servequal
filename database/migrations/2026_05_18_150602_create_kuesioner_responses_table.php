<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('kuesioner_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('responden_id')->constrained('users')->onDelete('cascade');
            $table->string('responden_nama', 100);
            $table->string('responden_nim', 20)->nullable();
            $table->string('responden_nidn', 20)->nullable();
            $table->enum('role', ['mahasiswa', 'dosen']);
            $table->string('kelas', 20)->nullable();
            $table->string('mata_kuliah', 100)->nullable();
            $table->foreignId('dosen_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('dosen_nama', 100)->nullable();
            $table->json('jawaban');
            $table->decimal('rata_rata', 3, 2)->nullable();
            $table->timestamps();

            $table->index('responden_id');
            $table->index('role');
            $table->index('dosen_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kuesioner_responses');
    }
};