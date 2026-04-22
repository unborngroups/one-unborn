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
        Schema::create('email_logs', function (Blueprint $table) {
    $table->id();
    $table->string('sender')->nullable();
    $table->string('subject')->nullable();
    $table->longText('body')->nullable();
    $table->string('attachment_path')->nullable();
    $table->enum('status', ['pending','processed','failed'])->default('pending');
    $table->text('error_message')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
