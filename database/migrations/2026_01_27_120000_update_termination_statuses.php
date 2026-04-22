<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            UPDATE terminations
            SET status = 
                CASE
                    WHEN termination_date IS NOT NULL THEN 'Terminated'
                    WHEN termination_request_date IS NOT NULL THEN 'Pending'
                    ELSE NULL
                END
        ");
    }

    public function down(): void
    {
        // Optionally revert status if needed
        // DB::statement("UPDATE terminations SET status = 'Pending' WHERE status IS NULL");
    }
};
