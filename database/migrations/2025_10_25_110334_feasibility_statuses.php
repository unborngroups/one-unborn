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
     // Vendor details (max 4 as per your sheet)
    $table->string('vendor1_name')->nullable();
    $table->string('vendor1_arc')->nullable();
    $table->string('vendor1_otc')->nullable();
    $table->string('vendor1_static_ip_cost')->nullable();
    $table->string('vendor1_delivery_timeline')->nullable();
    $table->string('vendor1_remarks')->nullable();

    $table->string('vendor2_name')->nullable();
    $table->string('vendor2_arc')->nullable();
    $table->string('vendor2_otc')->nullable();
    $table->string('vendor2_static_ip_cost')->nullable();
    $table->string('vendor2_delivery_timeline')->nullable();
    $table->string('vendor2_remarks')->nullable();

    $table->string('vendor3_name')->nullable();
    $table->string('vendor3_arc')->nullable();
    $table->string('vendor3_otc')->nullable();
    $table->string('vendor3_static_ip_cost')->nullable();
    $table->string('vendor3_delivery_timeline')->nullable();
    $table->string('vendor3_remarks')->nullable();

    $table->string('vendor4_name')->nullable();
    $table->string('vendor4_arc')->nullable();
    $table->string('vendor4_otc')->nullable();
    $table->string('vendor4_static_ip_cost')->nullable();
    $table->string('vendor4_delivery_timeline')->nullable();
    $table->string('vendor4_remarks')->nullable();

    // Status management
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
