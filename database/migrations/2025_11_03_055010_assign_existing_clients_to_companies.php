<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the first company (unborn technology)
        $company = DB::table('companies')->first();
        
        if ($company) {
            // Assign all existing clients (that don't have company_id) to this company
            DB::table('clients')
                ->whereNull('company_id')
                ->update(['company_id' => $company->id]);
            
            echo "Assigned existing clients to company: " . $company->company_name . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set company_id back to null for all clients
        DB::table('clients')->update(['company_id' => null]);
    }
};
