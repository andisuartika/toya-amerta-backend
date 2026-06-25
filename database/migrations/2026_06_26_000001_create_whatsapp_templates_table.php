<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->unique(); // 'tagihan' | 'konfirmasi_bayar'
            $table->text('template');
            $table->timestamps();
        });

        $now = now();

        DB::table('whatsapp_templates')->insert([
            [
                'type'       => 'tagihan',
                'template'   => "💧 *TAGIHAN AIR PDAM DESA SANGKET*\n\n"
                    . "Pelanggan: {nama}\n"
                    . "No. Pelanggan: {no_pelanggan}\n"
                    . "Periode: {periode}\n\n"
                    . "Meter Lalu: {meter_lalu} m³\n"
                    . "Meter Ini: {meter_ini} m³\n"
                    . "Pemakaian: {pemakaian} m³\n"
                    . "Harga/m³: Rp {harga_m3}\n\n"
                    . "TOTAL: Rp {total}\n\n"
                    . 'Mohon segera melakukan pembayaran. Terima kasih 🙏',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type'       => 'konfirmasi_bayar',
                'template'   => "✅ *KONFIRMASI PEMBAYARAN PDAM DESA SANGKET*\n\n"
                    . "Pelanggan: {nama}\n"
                    . "No. Kwitansi: {no_kwitansi}\n"
                    . "Periode: {periode}\n\n"
                    . "Jumlah Bayar: Rp {jumlah_bayar}\n"
                    . "Status Tagihan: {status}\n"
                    . "{sisa_block}\n"
                    . 'Terima kasih atas pembayaran Anda 🙏',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
