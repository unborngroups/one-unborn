<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize address components on gstins table and add uniqueness
        Schema::table('gstins', function (Blueprint $table) {
            if (!Schema::hasColumn('gstins', 'building_name')) {
                $table->string('building_name')->nullable()->after('principal_business_address');
            }
            if (!Schema::hasColumn('gstins', 'building_number')) {
                $table->string('building_number')->nullable()->after('building_name');
            }
            if (!Schema::hasColumn('gstins', 'floor_number')) {
                $table->string('floor_number')->nullable()->after('building_number');
            }
            if (!Schema::hasColumn('gstins', 'street')) {
                $table->string('street')->nullable()->after('floor_number');
            }
            if (!Schema::hasColumn('gstins', 'location')) {
                $table->string('location')->nullable()->after('street');
            }
            if (!Schema::hasColumn('gstins', 'district')) {
                $table->string('district')->nullable()->after('location');
            }
            if (!Schema::hasColumn('gstins', 'city')) {
                $table->string('city')->nullable()->after('district');
            }
            // Unique per entity to prevent duplicates for same client/vendor
            $table->unique(['entity_type', 'entity_id', 'gstin'], 'gstins_entity_gstin_unique');
        });

        // Drop master-level GST state column if it exists
        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'gst_state')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('gst_state');
            });
        }

        if (Schema::hasTable('vendors') && Schema::hasColumn('vendors', 'gst_state')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->dropColumn('gst_state');
            });
        }
    }

    public function down(): void
    {
        // Re-add dropped gst_state columns (nullable) and drop new address components + unique
        if (Schema::hasTable('clients') && !Schema::hasColumn('clients', 'gst_state')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('gst_state')->nullable();
            });
        }

        if (Schema::hasTable('vendors') && !Schema::hasColumn('vendors', 'gst_state')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->string('gst_state')->nullable();
            });
        }

        Schema::table('gstins', function (Blueprint $table) {
            // Drop unique index (Laravel will ignore if not exists)
            $table->dropUnique('gstins_entity_gstin_unique');

            foreach (['building_name','building_number','floor_number','street','location','district','city'] as $col) {
                if (Schema::hasColumn('gstins', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
