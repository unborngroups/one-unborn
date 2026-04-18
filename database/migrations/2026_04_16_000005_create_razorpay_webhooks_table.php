<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('razorpay_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_id', 100);
            $table->string('event_type', 100);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->json('payload');
            $table->boolean('processed')->default(false);
            $table->text('processing_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            
            $table->index(['event_type', 'processed']);
            $table->index(['webhook_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('razorpay_webhooks');
    }
};
