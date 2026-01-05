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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable(); 
            $table->string('company_name')->nullable();
        $table->string('company_email')->nullable();
        $table->string('expression_permission_email')->nullable();
        $table->json('feasibility_notifications')->nullable();

        $table->string('contact_no')->nullable();
        $table->string('website')->nullable();
        $table->text('address')->nullable();
        $table->string('gst_number')->nullable();
        $table->string('company_logo')->nullable();
        // 🌐 Social Media
        $table->string('linkedin_url')->nullable();
        $table->string('facebook_url')->nullable();
        $table->string('instagram_url')->nullable();
        $table->string('whatsapp_number')->nullable();
        $table->boolean('is_default')->default(0);


        // ✉️ Email Settings
        $table->string('mail_mailer')->nullable();
        $table->string('mail_host')->nullable();
        $table->string('mail_port')->nullable();
        $table->string('mail_username')->nullable();
        $table->string('mail_password')->nullable();
        $table->string('mail_encryption')->nullable();
        $table->string('mail_from_address')->nullable();
        $table->string('mail_from_name')->nullable();
        $table->string('mail_footer')->nullable();
            $table->string('mail_signature')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
