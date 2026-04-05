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
        Schema::create('deliverable_email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deliverable_id')->index();
            $table->unsignedBigInteger('sent_by')->nullable()->index();
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_from_email')->nullable();
            $table->string('sent_to_email')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->enum('received_status', ['pending','delivered','failed'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliverable_email_logs');
    }
};
