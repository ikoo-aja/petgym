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
            $table->string('hero_card_image')->nullable()->after('hero_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_landing_settings', function (Blueprint $table) {
            $table->dropColumn('hero_card_image');
        });
    }
};
