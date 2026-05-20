<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('penilaian_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            $table->string('mahasiswa_nama', 100);
            $table->string('mahasiswa_nim', 20);
            $table->json('nilai');
            $table->decimal('rata_rata', 3, 2)->nullable();
            $table->timestamps();

            $table->index('mahasiswa_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penilaian_fasilitas');
    }
};