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
        Schema::create('chat_group_users', function (Blueprint $table) {
            $table->bigIncrements('id'); // bigint UNSIGNED AUTO_INCREMENT
            $table->unsignedBigInteger('chat_group_id'); // NOT NULL
            $table->unsignedBigInteger('user_id');       // NOT NULL
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_group_users');
    }
};
