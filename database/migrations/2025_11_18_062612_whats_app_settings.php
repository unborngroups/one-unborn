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
         Schema::create('whats_app_settings', function (Blueprint $table) {
            $table->id();
            
            // Official WhatsApp API
            $table->string('official_phone')->nullable();
            $table->string('official_account_id')->nullable();
            $table->text('official_access_token')->nullable();
            $table->string('official_phone_id')->nullable();
            $table->boolean('official_enabled')->default(false);

            // Unofficial WhatsApp API (WaHub.pro)
            $table->string('unofficial_api_url')->nullable();
            $table->string('unofficial_mobile')->nullable();
            $table->string('unofficial_access_token')->nullable();
            $table->string('unofficial_instance_id')->nullable();
            $table->boolean('unofficial_enabled')->default(false);
            
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_settings');
    }
};
