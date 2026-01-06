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
        Schema::create('deliverable_plans', function (Blueprint $table) {
            $table->id();
             $table->foreignId('deliverable_id')->constrained('deliverables')->onDelete('cascade');
            $table->unsignedTinyInteger('link_number');
            $table->string('vendor_name')->nullable();
            $table->string('vendor_email')->nullable();
            $table->string('vendor_contact')->nullable();
            $table->string('circuit_id')->unique();

            $table->string('plans_name')->nullable();
            $table->string('speed_in_mbps_plan')->nullable();
            $table->integer('no_of_months_renewal')->nullable();
            $table->date('date_of_activation')->nullable();
            $table->date('date_of_expiry')->nullable();
            $table->string('sla')->nullable();
            // New fields
            $table->string('status_of_link')->nullable();
            $table->string('mode_of_delivery')->nullable();

            $table->string('client_circuit_id')->nullable();
            $table->string('client_feasibility')->nullable();
            $table->string('vendor_code')->nullable();
            $table->string('mtu')->nullable();
            $table->string('wifi_username')->nullable();
            $table->text('wifi_password')->nullable();
            $table->string('router_username')->nullable();  
            $table->text('router_password')->nullable();

            $table->string('payment_login_url')->nullable();
            $table->string('payment_quick_url')->nullable();
            $table->string('payment_account_or_username')->nullable();
            $table->string('payment_password')->nullable();

            $table->string('pppoe_username')->nullable();
            $table->text('pppoe_password')->nullable();
            $table->string('pppoe_vlan')->nullable();
            $table->string('dhcp_ip_address')->nullable();
            $table->string('dhcp_vlan')->nullable();
            $table->string('static_ip_address')->nullable();
            $table->string('static_vlan')->nullable();
            $table->string('network_ip')->nullable();
            $table->string('static_subnet_mask')->nullable();
            $table->string('static_gateway')->nullable();
            $table->string('usable_ips')->nullable();
            $table->string('remarks')->nullable();


            $table->timestamps();
            $table->unique(['deliverable_id', 'link_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliverable_plans');
    }
};
