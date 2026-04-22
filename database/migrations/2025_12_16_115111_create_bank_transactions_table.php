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
        Schema::create('bank_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
    $table->date('transaction_date');
    $table->enum('type',['Receipt','Payment']);
    $table->decimal('amount',15,2);
    $table->string('reference')->nullable();
    $table->string('narration')->nullable();
    $table->boolean('is_reconciled')->default(0);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
