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
              $columnsToDrop = [];
              if (Schema::hasColumn('system_settings', 'surepass_api_environment')) {
                $columnsToDrop[] = 'surepass_api_environment';
              }
              if (Schema::hasColumn('system_settings', 'whatsapp_default_number')) {
                $columnsToDrop[] = 'whatsapp_default_number';
              }
              if (Schema::hasColumn('system_settings', 'whatsapp_enabled')) {
                $columnsToDrop[] = 'whatsapp_enabled';
              }
              if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
              }
            });
          }
        };