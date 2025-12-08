<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
    public function up(): void
      {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('surepass_api_environment')->default('production')->after('surepass_api_token');
            $table->string('whatsapp_default_number')->nullable()->after('surepass_api_environment');
            $table->boolean('whatsapp_enabled')->default(false)->after('whatsapp_default_number');
          });
           }
              public function down(): void {
                Schema::table('system_settings', function (Blueprint $table) {
                    $table->dropColumn(['surepass_api_environment','whatsapp_default_number','whatsapp_enabled']);
             });
                }};