<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->string('nama_pemesan')->nullable()->after('user_id');
            $table->string('email_pemesan')->nullable()->after('nama_pemesan');
            $table->string('no_telepon', 30)->nullable()->after('email_pemesan');
            $table->text('alamat')->nullable()->after('no_telepon');
            $table->string('metode_pembayaran', 50)->nullable()->after('alamat');
            $table->timestamp('batas_bayar')->nullable()->after('status');
            $table->timestamp('paid_at')->nullable()->after('batas_bayar');
            $table->timestamp('used_at')->nullable()->after('paid_at');
            $table->foreignId('verified_by')->nullable()->after('used_at')->constrained('users')->nullOnDelete();
            $table->text('alasan_refund')->nullable()->after('verified_by');
            $table->timestamp('refund_requested_at')->nullable()->after('alasan_refund');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'nama_pemesan',
                'email_pemesan',
                'no_telepon',
                'alamat',
                'metode_pembayaran',
                'batas_bayar',
                'paid_at',
                'used_at',
                'verified_by',
                'alasan_refund',
                'refund_requested_at',
                'refund_processed_at',
            ]);
        });
    }
};