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
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->bigIncrements('id'); // bigint UNSIGNED AUTO_INCREMENT
            $table->string('name', 255); // NOT NULL
            $table->unsignedBigInteger('created_by'); // NOT NULL
            $table->unsignedBigInteger('company_id')->nullable(); // DEFAULT NULL
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Index + Foreign key (as per SQL dump)
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_groups');
    }
};
