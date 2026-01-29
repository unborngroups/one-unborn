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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->date('po_date');
            $table->unsignedBigInteger('feasibility_id');
            $table->string('feasibility_request_id', 50)->nullable();
            $table->decimal('arc_per_link', 10, 2);
            $table->decimal('otc_per_link', 10, 2); 
            $table->decimal('static_ip_cost_per_link', 10, 2);

            $table->decimal('arc_link_1', 12, 2)->nullable();
            $table->decimal('arc_link_2', 12, 2)->nullable();
            $table->decimal('arc_link_3', 12, 2)->nullable();
            $table->decimal('arc_link_4', 12, 2)->nullable();
            
            // OTC (One Time Charges) per link
            $table->decimal('otc_link_1', 12, 2)->nullable();
            $table->decimal('otc_link_2', 12, 2)->nullable();
            $table->decimal('otc_link_3', 12, 2)->nullable();
            $table->decimal('otc_link_4', 12, 2)->nullable();
            
            // Static IP Cost per link
            $table->decimal('static_ip_link_1', 12, 2)->nullable();
            $table->decimal('static_ip_link_2', 12, 2)->nullable();
            $table->decimal('static_ip_link_3', 12, 2)->nullable();
            $table->decimal('static_ip_link_4', 12, 2)->nullable();

            $table->integer('no_of_links');
            $table->string('import_file')->nullable();
            $table->integer('contract_period'); // in months
            $table->enum('status', ['Draft', 'Submitted', 'Approved', 'Cancelled'])->default('Draft');
            // $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('feasibility_id')->references('id')->on('feasibilities')->onDelete('cascade');
            $table->index('po_number');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('contract_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
