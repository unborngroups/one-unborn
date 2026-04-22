<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_approvals', function (Blueprint $table) {
            // Add approval_level column if it doesn't exist
            if (!Schema::hasColumn('finance_approvals', 'approval_level')) {
                $table->enum('approval_level', ['accountant', 'finance_manager'])->nullable()->after('status');
            }
            
            // Add approved_at column if it doesn't exist
            if (!Schema::hasColumn('finance_approvals', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('checker_id');
            }
            
            // Make remarks nullable if it exists and isn't already nullable
            if (Schema::hasColumn('finance_approvals', 'remarks')) {
                $table->text('remarks')->nullable()->change();
            }
            
            // Add indexes for better performance
            $table->index(['model_type', 'model_id', 'approval_level']);
            $table->index(['approval_level', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('finance_approvals', function (Blueprint $table) {
            // Only drop columns if they exist
            if (Schema::hasColumn('finance_approvals', 'approval_level')) {
                $table->dropColumn('approval_level');
            }
            
            if (Schema::hasColumn('finance_approvals', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            
            // Drop indexes if they exist
            $table->dropIndex(['model_type', 'model_id', 'approval_level']);
            $table->dropIndex(['approval_level', 'status']);
        });
    }
};
