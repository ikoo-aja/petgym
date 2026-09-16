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
        if (!Schema::hasColumn('tenant_landing_settings', 'brand_display_mode')) {
            Schema::table('tenant_landing_settings', function (Blueprint $table) {
                $table->string('brand_display_mode')->default('both')->after('template');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tenant_landing_settings', 'brand_display_mode')) {
            Schema::table('tenant_landing_settings', function (Blueprint $table) {
                $table->dropColumn('brand_display_mode');
            });
        }
    }
};
