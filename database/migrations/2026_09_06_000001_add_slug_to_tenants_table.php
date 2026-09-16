<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('subdomain');
        });

        // Isi slug dari label pertama subdomain (fitlife.workout.id -> fitlife)
        $tenants = DB::table('tenants')->whereNull('slug')->get();
        foreach ($tenants as $tenant) {
            $base = Str::before($tenant->subdomain, '.');
            $slug = Str::slug($base);

            // Jaga agar tetap unik bila ada subdomain dengan label pertama yang sama
            $exists = DB::table('tenants')->where('slug', $slug)->where('id', '!=', $tenant->id)->exists();
            if ($exists) {
                $slug = $slug . '-' . $tenant->id;
            }

            DB::table('tenants')->where('id', $tenant->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
