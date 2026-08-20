<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Member Portal features.
     */
    public function up(): void
    {
        // 1. Tambah kolom pada tabel locker_rentals jika belum lengkap
        Schema::table('locker_rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('locker_rentals', 'rental_type')) {
                $table->enum('rental_type', ['daily', 'monthly'])->default('daily')->after('member_id');
            }
            if (!Schema::hasColumn('locker_rentals', 'start_date')) {
                $table->date('start_date')->nullable()->after('rental_type');
            }
            if (!Schema::hasColumn('locker_rentals', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('locker_rentals', 'pin_code')) {
                $table->string('pin_code')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('locker_rentals', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0)->after('pin_code');
            }
            if (!Schema::hasColumn('locker_rentals', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid'])->default('paid')->after('amount');
            }
            if (!Schema::hasColumn('locker_rentals', 'status')) {
                $table->enum('status', ['active', 'expired', 'returned'])->default('active')->after('payment_status');
            }
        });

        // 2. Member PT Quotas Table
        if (!Schema::hasTable('member_pt_quotas')) {
            Schema::create('member_pt_quotas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
                $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
                $table->integer('total_sessions')->default(0);
                $table->integer('remaining_sessions')->default(0);
                $table->enum('status', ['active', 'exhausted'])->default('active');
                $table->timestamps();
            });
        }

        // 3. PT Bookings Table (Conflict Resolution Booking Engine)
        if (!Schema::hasTable('pt_bookings')) {
            Schema::create('pt_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
                $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
                $table->date('booking_date');
                $table->time('booking_time');
                $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
                $table->timestamps();
            });
        }

        // 4. Class RSVPs & Waitlist Queue Table
        if (!Schema::hasTable('class_rsvps')) {
            Schema::create('class_rsvps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('gym_class_id')->constrained('gym_classes')->cascadeOnDelete();
                $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
                $table->date('class_date');
                $table->enum('status', ['confirmed', 'waitlist', 'attended', 'noshow', 'cancelled'])->default('confirmed');
                $table->integer('queue_position')->nullable();
                $table->timestamps();
            });
        }

        // 5. Tambah kolom Penalti & User ID pada tabel members
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('members', 'membership_tier')) {
                $table->enum('membership_tier', ['basic', 'standard', 'premium'])->default('basic')->after('access_code');
            }
            if (!Schema::hasColumn('members', 'no_show_count')) {
                $table->integer('no_show_count')->default(0)->after('status');
            }
            if (!Schema::hasColumn('members', 'penalty_blocked_until')) {
                $table->timestamp('penalty_blocked_until')->nullable()->after('no_show_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rsvps');
        Schema::dropIfExists('pt_bookings');
        Schema::dropIfExists('member_pt_quotas');
    }
};
