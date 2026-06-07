<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_customer_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->enum('fee_type', [
                'biaya_pendaftaran',
                'biaya_instalasi',
                'biaya_meteran',
                'uang_jaminan',
                'lainnya',
            ]);
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->date('payment_date');
            $table->enum('payment_method', ['tunai', 'transfer', 'lainnya'])->default('tunai');
            $table->string('receipt_number', 50)->nullable()->unique();

            $table->foreignId('recorded_by')->constrained('users');
            // FK ke cash_transactions — otomatis dibuat saat input fee
            $table->foreignId('cash_transaction_id')->nullable()->constrained('cash_transactions')->nullOnDelete();

            $table->timestamps();

            $table->index('customer_id');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_customer_fees');
    }
};
