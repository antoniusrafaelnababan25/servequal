<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom role (ENUM) dan field lain
            $table->enum('role', ['super_admin', 'admin', 'dosen', 'mahasiswa'])->default('mahasiswa')->after('password');
            $table->string('username')->unique()->nullable()->after('id');
            $table->string('nidn')->nullable()->after('role');
            $table->string('nim')->nullable()->after('nidn');
            $table->string('kelas')->nullable()->after('nim');
            $table->string('jurusan')->nullable()->after('kelas');
            $table->string('avatar')->nullable()->after('jurusan');
            $table->date('tanggal_lahir')->nullable()->after('avatar');
            $table->timestamp('last_login')->nullable()->after('tanggal_lahir');
            $table->boolean('is_active')->default(1)->after('last_login');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'username', 'nidn', 'nim', 'kelas', 'jurusan', 'avatar', 'tanggal_lahir', 'last_login', 'is_active']);
        });
    }
};