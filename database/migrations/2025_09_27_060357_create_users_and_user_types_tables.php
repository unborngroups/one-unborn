<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        // Create user_types table first
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->boolean('require_otp_always')->default(false);
            $table->string('Description')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); 
            $table->string('password');
            $table->string('mobile')->nullable();
            // $table->tinyInteger('require_otp_always')->default(0);
            $table->date('Date_of_Birth')->nullable();
            $table->date('Date_of_Joining')->nullable();
            // 
           
            // 
           

            $table->string('official_email')->unique()->nullable();
            $table->string('personal_email')->nullable();
            //
            $table->string('email_template')->nullable();
            //
            $table->boolean('profile_created')->default(false);
            $table->unsignedBigInteger('user_type_id')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamp('last_activity')->nullable()->index();
            $table->timestamps();
            $table->rememberToken();
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
        });
    }
    public function down(): void
    {
         Schema::dropIfExists('users');
        Schema::dropIfExists('user_types');
    }
};
