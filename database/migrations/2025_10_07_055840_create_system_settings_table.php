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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
             $table->string('timezone')->default('Asia/Kolkata');
    $table->string('date_format')->default('DD-MM-YYYY');
    $table->string('language')->default('English');
    $table->string('currency_symbol')->default('₹');
    $table->string('fiscal_start_month')->default('April');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
