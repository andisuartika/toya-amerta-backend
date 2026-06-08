<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdam_bills', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('invoice_number');
            $table->foreignId('cash_transaction_id')->nullable()->after('notes')
                  ->constrained('cash_transactions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pdam_bills', function (Blueprint $table) {
            $table->dropForeign(['cash_transaction_id']);
            $table->dropColumn(['notes', 'cash_transaction_id']);
        });
    }
};
