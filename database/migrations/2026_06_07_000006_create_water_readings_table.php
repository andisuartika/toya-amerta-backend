<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('officer_id')->constrained('users');

            // Periode tagihan
            $table->year('period_year');
            $table->tinyInteger('period_month'); // 1-12

            // Meteran
            $table->decimal('previous_reading', 10, 2);
            $table->decimal('current_reading', 10, 2);
            // usage_m3 = current_reading - previous_reading (GENERATED STORED)
            $table->decimal('usage_m3', 10, 2)->storedAs('current_reading - previous_reading');

            // ── Snapshot tarif saat pencatatan ──
            // Disimpan agar tagihan historis tidak berubah jika tarif berubah di masa depan
            $table->foreignId('tariff_rate_id')->constrained('tariff_rates');
            $table->decimal('price_per_m3', 10, 2);
            $table->decimal('minimum_charge', 10, 2);
            $table->decimal('minimum_usage', 8, 2); // min m³ yang ditagih

            // Formula (dikerjakan UseCase, bukan GENERATED):
            // billable_usage = MAX(usage_m3, minimum_usage)
            // usage_cost     = billable_usage * price_per_m3
            // total_amount   = MAX(usage_cost, minimum_charge)
            $table->decimal('total_amount', 12, 2);

            $table->date('reading_date');
            $table->string('photo_url')->nullable();
            $table->text('notes')->nullable();

            $table->enum('payment_status', ['belum_bayar', 'lunas', 'sebagian'])->default('belum_bayar');

            // Notifikasi WA tagihan
            $table->timestamp('wa_sent_at')->nullable();

            // Audit
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Satu pelanggan hanya bisa punya satu pencatatan per bulan per tahun
            $table->unique(['customer_id', 'period_year', 'period_month']);

            $table->index(['period_year', 'period_month']);
            $table->index('payment_status');
            $table->index('reading_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_readings');
    }
};
