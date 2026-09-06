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
        Schema::create('tenant_landing_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained('tenants')->cascadeOnDelete();

            // Template landing (default; template lain bisa ditambahkan nanti)
            $table->string('template')->default('default');

            // Konten teks (bisa diedit SEMUA paket)
            $table->string('hero_title')->nullable();
            $table->string('hero_tagline')->nullable();
            $table->text('about_text')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();

            // Branding (paket Pro ke atas)
            $table->string('primary_color', 20)->nullable();
            $table->string('secondary_color', 20)->nullable();

            // Fitur unggulan & statistik (Pro: fitur, Enterprise: + statistik)
            $table->json('features')->nullable();
            $table->json('stats')->nullable();

            // Bagian halaman yang ditampilkan (Pro ke atas)
            $table->json('sections_enabled')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_landing_settings');
    }
};
