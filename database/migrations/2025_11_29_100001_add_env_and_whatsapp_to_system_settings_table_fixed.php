<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'surepass_api_environment')) {
                $table->string('surepass_api_environment')->default('production')->after('surepass_api_token');
            }
            if (!Schema::hasColumn('system_settings', 'whatsapp_default_number')) {
                $table->string('whatsapp_default_number')->nullable()->after('surepass_api_environment');
            }
            if (!Schema::hasColumn('system_settings', 'whatsapp_enabled')) {
                $table->boolean('whatsapp_enabled')->default(false)->after('whatsapp_default_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (Schema::hasColumn('system_settings', 'whatsapp_enabled')) {
                $table->dropColumn('whatsapp_enabled');
            }
            if (Schema::hasColumn('system_settings', 'whatsapp_default_number')) {
                $table->dropColumn('whatsapp_default_number');
            }
            if (Schema::hasColumn('system_settings', 'surepass_api_environment')) {
                $table->dropColumn('surepass_api_environment');
            }
        });
    }
};
