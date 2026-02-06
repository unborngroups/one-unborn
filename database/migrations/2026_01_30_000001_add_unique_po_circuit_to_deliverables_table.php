<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->unique(['purchase_order_id', 'circuit_id'], 'unique_po_circuit');
        });
    }

    public function down()
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->dropUnique('unique_po_circuit');
        });
    }
};
