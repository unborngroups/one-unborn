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
            $table->decimal('arc_per_link', 10, 2);
            $table->decimal('otc_per_link', 10, 2); 
            $table->decimal('static_ip_cost_per_link', 10, 2);
            $table->integer('no_of_links');
            $table->string('import_file')->nullable();
            $table->integer('contract_period'); // in months
            $table->enum('status', ['Draft', 'Submitted', 'Approved', 'Cancelled'])->default('Draft');
            // $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('feasibility_id')->references('id')->on('feasibilities')->onDelete('cascade');
            $table->index('po_number');
            $table->index('status');
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
