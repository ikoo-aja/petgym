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
        // 1. Lockers Master Data
        Schema::create('lockers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('locker_number');
            $table->enum('status', ['tersedia', 'terpakai', 'rusak'])->default('tersedia');
            $table->timestamps();
        });

        // 2. Locker Rentals Logs
        Schema::create('locker_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('locker_id')->constrained('lockers')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->timestamp('rented_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });

        // 3. Guests / Walk-in Leads
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('converted_to_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Lost & Found logs
        Schema::create('lost_founds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('location_found');
            $table->date('found_at');
            $table->enum('status', ['tercatat', 'diklaim'])->default('tercatat');
            $table->string('claimed_by_name')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });

        // 5. Receptionist Shift Logs
        Schema::create('receptionist_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('start_cash', 12, 2)->default(0);
            $table->decimal('end_cash', 12, 2)->nullable();
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });

        // 6. Trainer Sesi Check-in
        Schema::create('trainer_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->date('session_date');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_sessions');
        Schema::dropIfExists('receptionist_shifts');
        Schema::dropIfExists('lost_founds');
        Schema::dropIfExists('guests');
        Schema::dropIfExists('locker_rentals');
        Schema::dropIfExists('lockers');
    }
};
