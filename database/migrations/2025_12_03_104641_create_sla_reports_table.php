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
        Schema::create('sla_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_link_id');

            $table->integer('month'); // 1-12
            $table->integer('year');  // 2025 etc.

            $table->float('uptime_percentage')->nullable();
            $table->float('downtime_hours')->nullable();
            $table->float('avg_latency_ms')->nullable();
            $table->float('avg_packet_loss')->nullable();

            $table->boolean('breached')->default(false);
            $table->timestamps();

            $table->foreign('client_link_id')->references('id')->on('client_links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_reports');
    }
};
