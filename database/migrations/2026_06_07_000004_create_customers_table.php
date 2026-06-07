<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            // Nullable: pelanggan bisa ada sebelum punya akun login
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_number', 20)->unique();
            $table->string('name', 150);
            $table->text('address');
            $table->string('phone', 20)->nullable();
            $table->foreignId('zone_id')->constrained('zones');
            $table->foreignId('tariff_rate_id')->constrained('tariff_rates');
            $table->date('installation_date');
            $table->decimal('initial_meter', 10, 2)->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('zone_id');
            $table->index('tariff_rate_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
