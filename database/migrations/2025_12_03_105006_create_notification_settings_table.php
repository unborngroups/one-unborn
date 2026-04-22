<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();

            // Link to client or global
            $table->unsignedBigInteger('client_id')->nullable();

            // SLA notifications
            $table->boolean('notify_sla_breach')->default(true);

            // Real-time alerts
            $table->boolean('notify_link_down')->default(true);
            $table->boolean('notify_high_latency')->default(true);
            $table->boolean('notify_high_packet_loss')->default(true);

            // Thresholds
            $table->integer('latency_threshold')->default(150);        // ms
            $table->integer('packet_loss_threshold')->default(5);      // %

            // Cooldown to prevent email spam
            $table->integer('cooldown_minutes')->default(30);

            // Recipients
            $table->text('extra_recipients')->nullable(); // comma separated emails

            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
