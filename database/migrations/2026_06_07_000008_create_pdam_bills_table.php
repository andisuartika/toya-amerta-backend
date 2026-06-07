<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdam_bills', function (Blueprint $table) {
            $table->id();
            $table->year('period_year');
            $table->tinyInteger('period_month'); // 1-12

            $table->decimal('total_m3_from_pdam', 12, 2);
            $table->decimal('pdam_price_per_m3', 10, 2);
            // base_amount = total_m3_from_pdam * pdam_price_per_m3 (GENERATED STORED)
            $table->decimal('base_amount', 14, 2)
                  ->storedAs('total_m3_from_pdam * pdam_price_per_m3');

            $table->decimal('additional_charge', 12, 2)->default(0);
            // total_bill_amount diisi manual (base_amount + additional_charge + biaya admin dll)
            $table->decimal('total_bill_amount', 14, 2);

            $table->date('bill_date');
            $table->date('due_date');
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->date('payment_date')->nullable();
            $table->string('invoice_number', 50)->nullable()->unique();

            $table->foreignId('created_by')->constrained('users');

            $table->timestamps();

            $table->unique(['period_year', 'period_month']);
            $table->index(['period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdam_bills');
    }
};
