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
        Schema::table('admin_password_reset_requests', function (Blueprint $table) {
            // target_type: 'superadmin' jika pemohon adalah admin, 'admin' jika pemohon adalah staf/member
            $table->string('target_type')->default('superadmin')->after('tenant_id');
            $table->string('user_role')->nullable()->after('admin_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_password_reset_requests', function (Blueprint $table) {
            $table->dropColumn(['target_type', 'user_role']);
        });
    }
};
