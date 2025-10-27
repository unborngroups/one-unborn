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
        Schema::create('feasibility_statuses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('feasibility_id')->constrained('feasibilities')->onDelete('cascade');
    $table->string('vendor_name')->nullable();
    $table->string('arc')->nullable();
    $table->string('otc')->nullable();
    $table->string('static_ip_cost')->nullable();
    $table->string('delivery_timeline')->nullable();
    $table->enum('status', ['Open', 'InProgress', 'Closed'])->default('Open');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feasibility_statuses');
    }
};
