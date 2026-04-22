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
         Schema::create('account_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Asset','Liability','Income','Expense','Equity']);
            $table->timestamps();
        });

         Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_group_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('account_name');
            $table->string('account_code')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('balance_type', ['Debit','Credit']);
            $table->boolean('is_active')->default(true);

            // Maker–Checker
            $table->enum('status', [
                'draft',
                'pending_checker',
                'approved',
                'rejected',
                'locked'
            ])->default('draft');
            $table->boolean('is_locked')->default(false);

            $table->foreignId('maker_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('checker_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('maker_submitted_at')->nullable();
            $table->timestamp('checker_approved_at')->nullable();
            $table->timestamp('locked_at')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('accounts');
        Schema::dropIfExists('account_groups');
    }
};
