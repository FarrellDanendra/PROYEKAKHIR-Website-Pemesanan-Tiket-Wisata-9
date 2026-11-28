<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wisatas', function (Blueprint $table) {
            $table->date('tanggal_tutup_mulai')->nullable()->after('tanggal_buka');
            $table->date('tanggal_tutup_selesai')->nullable()->after('tanggal_tutup_mulai');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wisatas', function (Blueprint $table) {
            $table->dropColumn(['tanggal_tutup_mulai', 'tanggal_tutup_selesai']);
            $table->dropSoftDeletes();
        });
    }
};