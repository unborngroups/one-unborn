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
        Schema::create('gstins', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // 'client' or 'vendor'
            $table->unsignedBigInteger('entity_id'); // client_id or vendor_id
            $table->string('gstin', 15)->unique();
            $table->string('trade_name')->nullable();
            $table->text('principal_business_address')->nullable();
            $table->string('state')->nullable();
            $table->string('state_code', 2)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('status')->default('Active'); // Active/Inactive
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Add indexes
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gstins');
    }
};
