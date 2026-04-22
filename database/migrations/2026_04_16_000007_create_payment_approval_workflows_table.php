<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_batch_id')->constrained()->onDelete('cascade');
            $table->enum('approval_level', ['accountant', 'finance_manager']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['payment_batch_id', 'approval_level']);
            $table->index(['status', 'approval_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_approval_workflows');
    }
};
