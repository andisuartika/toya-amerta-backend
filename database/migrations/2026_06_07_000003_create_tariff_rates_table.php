<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('price_per_m3', 10, 2);
            // Minimum tagihan dalam Rupiah — total_amount tidak boleh < ini
            $table->decimal('minimum_charge', 10, 2)->default(0);
            // Minimum pemakaian dalam m³ — jika usage < ini, dihitung sebesar minimum_usage
            $table->decimal('minimum_usage', 8, 2)->default(1);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_rates');
    }
};
