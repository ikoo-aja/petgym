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
        Schema::table('tenant_landing_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('tenant_landing_settings', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('brand_display_mode');
            }
            if (!Schema::hasColumn('tenant_landing_settings', 'maintenance_title')) {
                $table->string('maintenance_title')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('tenant_landing_settings', 'maintenance_message')) {
                $table->text('maintenance_message')->nullable()->after('maintenance_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_landing_settings', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('tenant_landing_settings', 'is_active')) {
                $columnsToDrop[] = 'is_active';
            }
            if (Schema::hasColumn('tenant_landing_settings', 'maintenance_title')) {
                $columnsToDrop[] = 'maintenance_title';
            }
            if (Schema::hasColumn('tenant_landing_settings', 'maintenance_message')) {
                $columnsToDrop[] = 'maintenance_message';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
