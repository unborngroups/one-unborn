<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('purchase_invoices', function (Blueprint $table) {
        $table->json('raw_json')->nullable()->after('email_log_id');
    });
}

public function down()
{
    Schema::table('purchase_invoices', function (Blueprint $table) {
        $table->dropColumn('raw_json');
    });
}


};
