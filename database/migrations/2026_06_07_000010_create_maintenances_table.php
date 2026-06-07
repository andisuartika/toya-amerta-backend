<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('location', 255);
            $table->enum('category', [
                'pipa_bocor',
                'meteran_rusak',
                'pompa',
                'reservoir',
                'instalasi_baru',
                'lainnya',
            ]);

            // Zone & customer bisa null (kerusakan umum tidak terkait pelanggan tertentu)
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('reported_by')->constrained('users');

            $table->enum('priority', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');
            $table->enum('status', ['dilaporkan', 'dalam_proses', 'selesai', 'ditunda'])->default('dilaporkan');

            $table->text('description')->nullable();
            $table->string('photo_before_url')->nullable();
            $table->string('photo_after_url')->nullable();

            $table->date('reported_date');
            $table->date('handled_date')->nullable();
            $table->date('completed_date')->nullable();

            $table->decimal('material_cost', 12, 2)->nullable();
            $table->decimal('labor_cost', 12, 2)->nullable();
            // total_cost pakai COALESCE agar null tidak merusak kalkulasi (GENERATED STORED)
            $table->decimal('total_cost', 12, 2)
                  ->storedAs('COALESCE(material_cost, 0) + COALESCE(labor_cost, 0)');

            // FK ke cash_transactions — otomatis dibuat saat status = selesai
            $table->foreignId('cash_transaction_id')->nullable()->constrained('cash_transactions')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('reported_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
