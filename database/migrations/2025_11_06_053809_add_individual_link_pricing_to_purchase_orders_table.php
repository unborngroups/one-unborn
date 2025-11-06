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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Add individual link pricing fields to support multi-vendor validation
            
            // ARC (Annual Rental Charges) per link
            $table->decimal('arc_link_1', 12, 2)->nullable()->after('static_ip_cost_per_link');
            $table->decimal('arc_link_2', 12, 2)->nullable()->after('arc_link_1');
            $table->decimal('arc_link_3', 12, 2)->nullable()->after('arc_link_2');
            $table->decimal('arc_link_4', 12, 2)->nullable()->after('arc_link_3');
            
            // OTC (One Time Charges) per link
            $table->decimal('otc_link_1', 12, 2)->nullable()->after('arc_link_4');
            $table->decimal('otc_link_2', 12, 2)->nullable()->after('otc_link_1');
            $table->decimal('otc_link_3', 12, 2)->nullable()->after('otc_link_2');
            $table->decimal('otc_link_4', 12, 2)->nullable()->after('otc_link_3');
            
            // Static IP Cost per link
            $table->decimal('static_ip_link_1', 12, 2)->nullable()->after('otc_link_4');
            $table->decimal('static_ip_link_2', 12, 2)->nullable()->after('static_ip_link_1');
            $table->decimal('static_ip_link_3', 12, 2)->nullable()->after('static_ip_link_2');
            $table->decimal('static_ip_link_4', 12, 2)->nullable()->after('static_ip_link_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop individual link pricing fields
            $table->dropColumn([
                'arc_link_1', 'arc_link_2', 'arc_link_3', 'arc_link_4',
                'otc_link_1', 'otc_link_2', 'otc_link_3', 'otc_link_4', 
                'static_ip_link_1', 'static_ip_link_2', 'static_ip_link_3', 'static_ip_link_4'
            ]);
        });
    }
};
