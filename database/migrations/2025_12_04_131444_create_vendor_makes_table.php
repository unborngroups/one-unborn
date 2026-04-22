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
        Schema::create('vendor_makes', function (Blueprint $table) {
            $table->id();
            $table->string('make_name')->index();
            $table->string('company_name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_makes');
    }
};
