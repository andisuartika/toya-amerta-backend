<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_reading_id')->constrained('water_readings');
            $table->foreignId('customer_id')->constrained('customers');
            // Nullable: admin juga bisa konfirmasi
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['tunai', 'transfer', 'lainnya'])->default('tunai');
            $table->enum('status', ['lunas', 'sebagian'])->default('lunas');

            // Nomor kwitansi — di-generate otomatis atau manual
            $table->string('receipt_number', 50)->nullable()->unique();
            $table->text('notes')->nullable();

            // Kapan notifikasi konfirmasi WA dikirim
            $table->timestamp('wa_sent_at')->nullable();

            // Audit
            $table->foreignId('recorded_by')->constrained('users');

            $table->timestamps();

            $table->index('payment_date');
            $table->index('customer_id');
            $table->index('water_reading_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_records');
    }
};
