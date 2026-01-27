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
        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deliverable_id')
          ->constrained()
          ->cascadeOnDelete();
            $table->string('circuit_id')->nullable();

        $table->date('date_of_renewal');          // user selected
    $table->integer('renewal_months');        // 1,3,6,12
    $table->date('new_expiry_date');          // auto calculated
    $table->date('alert_date');   
    $table->enum('status', ['Active', 'Inactive'])->default('Active');
      // expiry - 1 day

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewals');
    }
};
