<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kuesioner_responses', function (Blueprint $table) {
            $table->foreignId('periode_id')
                ->nullable()
                ->after('id')
                ->constrained('kuesioner_periode')
                ->onDelete('set null');
            $table->index('periode_id');
        });
    }

    public function down(): void
    {
        Schema::table('kuesioner_responses', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });
    }
};