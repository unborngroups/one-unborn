<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_learning_logs', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_name_raw')->nullable();
            $table->string('gstin')->nullable();
            $table->unsignedBigInteger('matched_vendor_id')->nullable();
            $table->float('confidence')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_learning_logs');
    }
};
