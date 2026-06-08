<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdam_bills', function (Blueprint $table) {
            $table->json('tier_details')->nullable()->after('total_bill_amount');
        });
    }

    public function down(): void
    {
        Schema::table('pdam_bills', function (Blueprint $table) {
            $table->dropColumn('tier_details');
        });
    }
};
