<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('client_link_id')->nullable();

            $table->string('alert_type'); // SLA_BREACH / LINK_DOWN / HIGH_LATENCY / PACKET_LOSS
            $table->string('message', 500)->nullable();
            $table->string('sent_to_email')->nullable();
            $table->boolean('status')->default(true); // true = sent, false = failed
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('client_link_id')->references('id')->on('client_links')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
