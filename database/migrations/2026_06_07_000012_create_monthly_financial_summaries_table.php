<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cache rekap keuangan bulanan — di-generate setiap tanggal 1 bulan berikutnya
        // Formula:
        // total_income    = total_paid_amount + total_new_customer_fees + total_other_income
        // total_expense   = pdam_bill_amount + total_maintenance_cost + total_other_expense
        // net_profit_loss = total_income - total_expense
        Schema::create('monthly_financial_summaries', function (Blueprint $table) {
            $table->id();
            $table->year('period_year');
            $table->tinyInteger('period_month'); // 1-12

            // Tagihan air pelanggan
            $table->unsignedInteger('total_customers_billed')->default(0);
            $table->decimal('total_usage_m3', 14, 2)->default(0);
            $table->decimal('total_billed_amount', 14, 2)->default(0);   // total tagihan ke pelanggan
            $table->decimal('total_paid_amount', 14, 2)->default(0);     // yang sudah dibayar
            $table->decimal('total_unpaid_amount', 14, 2)->default(0);   // yang belum dibayar

            // Pemasukan lain
            $table->decimal('total_new_customer_fees', 14, 2)->default(0);
            $table->decimal('total_other_income', 14, 2)->default(0);

            // Pengeluaran
            $table->decimal('pdam_bill_amount', 14, 2)->default(0);
            $table->decimal('total_maintenance_cost', 14, 2)->default(0);
            $table->decimal('total_other_expense', 14, 2)->default(0);

            // Rekap
            $table->decimal('total_income', 14, 2)->default(0);
            $table->decimal('total_expense', 14, 2)->default(0);
            $table->decimal('net_profit_loss', 14, 2)->default(0); // bisa negatif

            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();

            $table->unique(['period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_financial_summaries');
    }
};
