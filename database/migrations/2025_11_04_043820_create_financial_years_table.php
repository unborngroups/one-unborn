<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20); // e.g., "2024-25"
            $table->date('start_date'); // e.g., 2024-04-01
            $table->date('end_date'); // e.g., 2025-03-31
            $table->boolean('is_active')->default(false);
            $table->integer('current_year'); // e.g., 2024
            $table->string('year_format', 10)->default('YY-YY'); // Format: YY-YY, YYYY-YY, etc.
            $table->timestamps();
            
            $table->index('is_active');
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_years');
    }
};
