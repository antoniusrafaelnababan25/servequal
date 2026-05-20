<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('cascade');
            $table->string('nama_prodi');
            $table->enum('jenjang', ['sarjana', 'pascasarjana', 'internasional'])->default('sarjana');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prodi');
    }
};