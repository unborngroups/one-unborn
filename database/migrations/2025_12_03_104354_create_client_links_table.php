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
        Schema::create('client_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('router_id')->nullable();
            $table->string('interface_name');
            $table->string('link_type')->default('ILL'); // ILL, Broadband, P2P etc.
            $table->string('bandwidth')->nullable(); // e.g., 100Mbps
            $table->string('service_id')->nullable(); // For future CRM/Service mapping
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('router_id')->references('id')->on('mikrotik_routers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_links');
    }
};
