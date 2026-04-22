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
        Schema::create('link_monitoring_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_link_id');

            // Live metrics
            $table->float('latency_ms')->nullable();
            $table->float('packet_loss')->nullable();
            $table->float('upload_mbps')->nullable();
            $table->float('download_mbps')->nullable();

            // Router status
            $table->boolean('is_link_up')->default(true);

            $table->timestamp('collected_at')->nullable();
            $table->timestamps();

            $table->foreign('client_link_id')->references('id')->on('client_links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_monitoring_data');
    }
};
