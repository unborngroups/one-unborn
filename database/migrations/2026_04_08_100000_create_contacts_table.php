<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('contact_type', 20);
            $table->string('name');
            $table->string('area')->nullable();
            $table->string('state')->nullable();
            $table->string('contact1', 20);
            $table->string('contact2', 20)->nullable();
            $table->string('remarks')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->index(['contact_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
