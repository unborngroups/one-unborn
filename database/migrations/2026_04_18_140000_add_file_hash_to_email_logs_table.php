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
        Schema::table('email_logs', function (Blueprint $table) {
            $table->string('file_hash')->nullable()->after('error_message');
            $table->string('source')->default('gmail')->after('file_hash');
            $table->boolean('is_active')->default(true)->after('source');
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['file_hash', 'source', 'is_active', 'company_id']);
        });
    }
};
