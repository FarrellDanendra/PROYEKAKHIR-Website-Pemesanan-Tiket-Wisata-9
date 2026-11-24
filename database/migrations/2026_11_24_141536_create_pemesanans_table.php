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
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket', 50)->unique();
            $table->foreignId('wisata_id')->constrained('wisatas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_kunjungan');
            $table->unsignedInteger('jumlah_tiket');
            $table->decimal('total_harga', 14, 2);
            $table->string('status', 30)->default('pending'); // pending, dikonfirmasi, selesai, dibatalkan
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};