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
            $table->string('hero_image')->nullable()->after('hero_tagline');
            $table->string('about_image')->nullable()->after('about_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_landing_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_image', 'about_image']);
        });
    }
};
