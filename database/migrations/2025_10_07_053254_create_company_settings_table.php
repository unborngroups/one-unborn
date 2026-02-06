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
        $table->string('exception_permission_email')->nullable();
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

        // General Notification
            $table->string('general_mail_host')->nullable();
            $table->string('general_mail_port')->nullable();
            $table->string('general_mail_username')->nullable();
            $table->string('general_mail_password')->nullable();
            $table->string('general_mail_encryption')->nullable();
            $table->string('general_mail_from_address')->nullable();
            $table->string('general_mail_from_name')->nullable();
            $table->string('general_mail_footer')->nullable();
            $table->string('general_mail_signature')->nullable();

            // Delivery Notification
            $table->string('delivery_mail_host')->nullable();
            $table->string('delivery_mail_port')->nullable();
            $table->string('delivery_mail_username')->nullable();
            $table->string('delivery_mail_password')->nullable();
            $table->string('delivery_mail_encryption')->nullable();
            $table->string('delivery_mail_from_address')->nullable();
            $table->string('delivery_mail_from_name')->nullable();
            $table->string('delivery_mail_footer')->nullable();
            $table->string('delivery_mail_signature')->nullable();

            $table->boolean('delivery_email_check')->default(0);
            // Invoice Sending
            $table->string('invoice_mail_host')->nullable();
            $table->string('invoice_mail_port')->nullable();
            $table->string('invoice_mail_username')->nullable();
            $table->string('invoice_mail_password')->nullable();
            $table->string('invoice_mail_encryption')->nullable();
            $table->string('invoice_mail_from_address')->nullable();
            $table->string('invoice_mail_from_name')->nullable();
            $table->string('invoice_mail_footer')->nullable();
            $table->string('invoice_mail_signature')->nullable();

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
