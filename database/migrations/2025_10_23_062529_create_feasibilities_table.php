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
        Schema::create('feasibilities', function (Blueprint $table) {
            $table->id();
            $table->string('type_of_service');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('pincode');
            $table->string('state');
            $table->string('district');
            $table->string('area');
            $table->text('address')->nullable();
            $table->string('spoc_name');
            $table->string('spoc_contact1');
            $table->string('spoc_contact2')->nullable();
            $table->string('spoc_email')->nullable();
            $table->integer('no_of_links')->nullable();
            $table->string('vendor_type');
            $table->string('speed');
            $table->date('expected_delivery')->nullable();
            $table->date('expected_activation')->nullable();
            $table->boolean('hardware_required')->default(false);
            $table->string('hardware_model_name')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feasibilities');
    }
};
