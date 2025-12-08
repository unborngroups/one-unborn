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
        Schema::create('assets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained('companies');
    $table->foreignId('asset_type_id')->constrained('asset_types');
    $table->foreignId('make_id')->constrained('make_types');
    $table->foreignId('vendor_id')->nullable()->constrained('vendors');
    $table->string('asset_id')->unique();
    $table->string('model')->nullable();
    $table->string('serial_no')->nullable();
    $table->string('mac_no')->nullable();
    $table->date('purchase_date')->nullable();
    $table->date('warranty_date')->nullable();
    $table->string('po_no')->nullable();
    $table->decimal('mrp', 10, 2)->nullable();
    $table->decimal('purchase_cost', 10, 2)->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset');
    }
};
