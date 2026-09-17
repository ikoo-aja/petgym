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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('plan_name')->nullable()->after('amount');
            $table->integer('duration_months')->default(1)->after('plan_name');
            $table->string('payment_method')->nullable()->after('duration_months');
            $table->text('notes')->nullable()->after('proof_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['plan_name', 'duration_months', 'payment_method', 'notes']);
        });
    }
};
