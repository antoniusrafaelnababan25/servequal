<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_nama', 100);
            $table->enum('user_role', ['admin', 'dosen', 'mahasiswa']);
            $table->string('aktivitas', 255);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('user_role');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_aktivitas');
    }
};