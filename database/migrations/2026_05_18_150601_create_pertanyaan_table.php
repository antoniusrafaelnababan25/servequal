<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->enum('dimensi', ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy']);
            $table->text('teks');
            $table->enum('target_role', ['mahasiswa', 'dosen']);
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->index('dimensi');
            $table->index('target_role');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pertanyaan');
    }
};