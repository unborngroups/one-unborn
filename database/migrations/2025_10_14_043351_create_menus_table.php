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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('module_name')->nullable(); 
            $table->string('user_type');    // ex: Admin, HR, Employee, etc.
            $table->string('name');         // Menu name (ex: Manage User)
            $table->string('sub_section')->nullable();         // Sub-section
            $table->string('route')->nullable(); // Route name (ex: users.index)
            //  $table->unsignedBigInteger('user_type_id')->nullable();
            $table->string('icon')->nullable();  // Optional icon name
            $table->boolean('can_menu')->default(0);
            $table->boolean('can_add')->default(0);
            $table->boolean('can_edit')->default(0);
            $table->boolean('can_delete')->default(0);
            $table->boolean('can_view')->default(1);
            $table->timestamps();
        });

         Schema::create('user_menu_privileges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->boolean('can_menu')->default(false);
            $table->boolean('can_add')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_view')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_menu_privileges');
        Schema::dropIfExists('menus');
    }
};
