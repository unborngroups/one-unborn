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
        Schema::table('feasibilities', function (Blueprint $table) {
            $table->string('static_ip_duration')->nullable()->after('static_ip_subnet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feasibilities', function (Blueprint $table) {
            $table->dropColumn('static_ip_duration');
        });
    }
};
