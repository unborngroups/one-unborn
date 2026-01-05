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
        Schema::create('deliverables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('feasibility_id')
                ->constrained('feasibilities')
                ->onDelete('cascade');

            $table->foreignId('purchase_order_id')
                ->nullable()
                ->constrained('purchase_orders')
                ->onDelete('set null');

            $table->string('circuit_id')->unique();

            $table->text('client_circuit_id')->nullable();
            $table->text('client_feasibility')->nullable();
            $table->text('vendor_code')->nullable();

            $table->string('status')->default('Open');
            $table->string('delivery_id')->nullable()->unique();

            $table->text('site_address')->nullable();
            $table->string('local_contact')->nullable();
            $table->string('state')->nullable();
            $table->text('gst_number')->nullable();
            $table->text('link_type')->nullable();
            $table->text('speed_in_mbps')->nullable();
            $table->integer('no_of_links')->nullable();

            // Vendor
            $table->string('vendor')->nullable();
            $table->string('po_number')->nullable()->index();
            $table->date('po_date')->nullable();
            $table->decimal('arc_cost', 12, 2)->nullable();
            $table->decimal('otc_cost', 12, 2)->nullable();
            $table->decimal('static_ip_cost', 12, 2)->nullable();

            // 🔥 PLAN INFO (1–4 links)
            for ($i = 1; $i <= 4; $i++) {
                $table->text("plans_name_{$i}")->nullable();
                $table->text("speed_in_mbps_plan_{$i}")->nullable();
                $table->bigInteger("no_of_months_renewal_{$i}")->nullable();
                $table->date("date_of_activation_{$i}")->nullable();
                $table->date("date_of_expiry_{$i}")->nullable();
                $table->text("sla_{$i}")->nullable();
                $table->text("mode_of_delivery_{$i}")->nullable();
                $table->text("pppoe_username_{$i}")->nullable();
                $table->text("pppoe_password_{$i}")->nullable();
                $table->text("pppoe_vlan_{$i}")->nullable();
                $table->text("dhcp_ip_address_{$i}")->nullable();
                $table->text("dhcp_vlan_{$i}")->nullable();
                $table->text("static_ip_address_{$i}")->nullable();
                $table->text("static_vlan_{$i}")->nullable();
                $table->text("static_subnet_mask_{$i}")->nullable();
                $table->text("static_gateway_{$i}")->nullable();
                $table->text("static_vlan_tag_{$i}")->nullable();
                // Payment & Network (convert to text to reduce row size)
                $table->text("payment_login_url_{$i}")->nullable();
                $table->text("payment_quick_url_{$i}")->nullable();
                $table->text("payment_account_or_username_{$i}")->nullable();
                $table->text("payment_password_{$i}")->nullable();
                $table->text("mtu_{$i}")->nullable();
                $table->text("wifi_username_{$i}")->nullable();
                $table->text("wifi_password_{$i}")->nullable();
                $table->text("router_username_{$i}")->nullable();
                $table->text("router_password_{$i}")->nullable();
            }

            $table->string('lan_ip_1')->nullable();
            $table->string('lan_ip_2')->nullable();
            $table->string('lan_ip_3')->nullable();
            $table->string('lan_ip_4')->nullable();

            $table->enum('ipsec', ['Yes', 'No'])->default('No');
            $table->string('phase_1')->nullable();
            $table->string('phase_2')->nullable();
            $table->string('ipsec_interface')->nullable();

            $table->string('status_of_link')->default('Pending');

            $table->decimal('otc_extra_charges', 10, 2)->nullable();
            $table->string('otc_bill_file')->nullable();
            $table->string('export_file')->nullable();

            $table->string('asset_id')->nullable();
            $table->string('asset_serial_no')->nullable();
            $table->string('asset_mac_no')->nullable();

            $table->timestamp('delivered_at')->nullable();
            $table->string('delivered_by')->nullable();
            $table->text('delivery_notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('feasibility_id');
            $table->index('delivery_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliverables');
    }
};
