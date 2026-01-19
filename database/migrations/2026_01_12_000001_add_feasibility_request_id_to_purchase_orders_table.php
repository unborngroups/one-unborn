<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('feasibility_request_id', 50)->nullable()->after('feasibility_id');
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('feasibility_request_id');
        });
    }
};
