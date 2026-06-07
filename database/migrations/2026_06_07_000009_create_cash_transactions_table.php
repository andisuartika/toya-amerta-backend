<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->enum('type', ['masuk', 'keluar']);
            // Contoh: "Pembayaran Tagihan Air", "Tagihan PDAM Pusat", "Biaya Maintenance"
            $table->string('category', 100);
            $table->decimal('amount', 14, 2);
            $table->text('description')->nullable();

            // Polymorphic reference ke sumber transaksi
            // reference_type: "payment_records" | "maintenances" | "new_customer_fees" | "pdam_bills"
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->string('attachment_url')->nullable();
            $table->foreignId('recorded_by')->constrained('users');

            $table->timestamps();

            $table->index(['transaction_date', 'type']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
