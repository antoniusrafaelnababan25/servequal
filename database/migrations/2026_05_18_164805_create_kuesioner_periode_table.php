<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('kuesioner_periode', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode', 100);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['draft', 'aktif', 'tutup'])->default('draft');
            $table->string('target_jurusan', 100)->nullable();
            $table->text('tujuan')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index('status');
            $table->index('is_active');
            $table->index('tanggal_mulai');
            $table->index('tanggal_selesai');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kuesioner_periode');
    }
};