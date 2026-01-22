<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terminations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('circuit_id');
            $table->string('company_name');
            $table->string('address');
            $table->string('bandwidth')->nullable();
            $table->string('asset_id')->nullable();
            $table->string('asset_mac')->nullable();
            $table->string('asset_serial')->nullable();
            $table->date('date_of_activation')->nullable();
            $table->date('date_of_last_renewal')->nullable();
            $table->date('date_of_expiry')->nullable();
            $table->date('termination_request_date')->nullable();
            $table->string('termination_requested_by')->nullable();
            $table->string('termination_request_document')->nullable();
            $table->date('termination_date')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminations');
    }
};
