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
        Schema::create('debit_notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_invoice_id')->constrained();
    $table->string('debit_note_no');
    $table->date('date');
    $table->decimal('amount', 20, 2);
    $table->text('reason')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_notes');
    }
};
