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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('profile_photo')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('designation')->nullable();
            $table->date('Date_of_Birth')->nullable();
            $table->string('official_email')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('aadhaar_number')->nullable();
            $table->string('aadhaar_upload')->nullable();
            $table->string('pan')->nullable();
            $table->string('pan_upload')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
