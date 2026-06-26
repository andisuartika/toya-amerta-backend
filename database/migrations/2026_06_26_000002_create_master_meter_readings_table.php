<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_meter_readings', function (Blueprint $table) {
            $table->id();

            // Periode pencatatan
            $table->year('period_year');
            $table->tinyInteger('period_month'); // 1-12

            // Meteran induk — sumber air sebelum disebarkan ke seluruh pelanggan
            $table->decimal('previous_reading', 12, 2);
            $table->decimal('current_reading', 12, 2);
            $table->decimal('usage_m3', 12, 2)->storedAs('current_reading - previous_reading');

            $table->date('reading_date');
            $table->string('photo_url')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')->constrained('users');

            $table->timestamps();

            // Satu pencatatan meteran induk per bulan per tahun
            $table->unique(['period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_meter_readings');
    }
};
