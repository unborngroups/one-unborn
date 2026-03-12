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
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('deliverable_id')->nullable()->after('id');
        });
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('deliverable_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('deliverable_id');
        });
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('deliverable_id');
        });
    }
};
