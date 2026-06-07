<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Log semua notifikasi WA — untuk debugging & audit trail pengiriman
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();

            // Polymorphic: bisa dari water_readings atau payment_records
            $table->string('notifiable_type', 50);  // "water_readings" | "payment_records"
            $table->unsignedBigInteger('notifiable_id');

            $table->string('recipient_phone', 20);
            $table->enum('type', [
                'tagihan',           // notif tagihan bulanan baru
                'konfirmasi_bayar',  // notif setelah pembayaran dikonfirmasi
                'pengingat',         // reminder jatuh tempo
                'lainnya',
            ]);
            $table->text('message');  // isi pesan WA yang dikirim

            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();

            // Provider WA — untuk antisipasi multi-provider di masa depan
            $table->string('provider', 30)->default('fonnte');
            // Response raw dari provider API
            $table->json('provider_response')->nullable();

            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['status', 'created_at']);
            $table->index('recipient_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
