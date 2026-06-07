<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officer_zones', function (Blueprint $table) {
            $table->foreignId('officer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();

            $table->primary(['officer_id', 'zone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_zones');
    }
};
