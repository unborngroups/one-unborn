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
        Schema::create('finance_gst_settings', function (Blueprint $table) {
            $table->id();
             $table->boolean('gst_enabled')->default(0);

            $table->string('gst_number')->nullable();
            $table->string('state_code', 10)->nullable();

            $table->decimal('cgst_rate', 5, 2)->default(0);
            $table->decimal('sgst_rate', 5, 2)->default(0);
            $table->decimal('igst_rate', 5, 2)->default(0);

            $table->enum('calculation_type', ['inclusive', 'exclusive'])
                  ->default('exclusive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_gst_settings');
    }
};
