<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_records', function (Blueprint $table) {
            $table->foreignId('cash_transaction_id')->nullable()->after('recorded_by')
                  ->constrained('cash_transactions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_records', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\CashTransaction::class);
            $table->dropColumn('cash_transaction_id');
        });
    }
};
