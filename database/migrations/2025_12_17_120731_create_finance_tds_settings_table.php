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
        Schema::create('finance_tds_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('tds_enabled')->default(0);

            $table->string('section')->nullable();      // 194C, 194J etc
            $table->decimal('tds_rate', 5, 2)->default(0);

            $table->decimal('threshold_amount', 10, 2)->default(0);

            $table->enum('deduction_on', ['payment', 'invoice'])
                  ->default('payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_tds_settings');
    }
};
