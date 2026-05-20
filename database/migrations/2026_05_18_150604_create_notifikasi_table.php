<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('title', 200);
            $table->text('message');
            $table->enum('target_role', ['admin', 'dosen', 'mahasiswa', 'all']);
            $table->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('is_read')->default(0);
            $table->timestamps();

            $table->index('target_role');
            $table->index('target_user_id');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifikasi');
    }
};