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
        // 1. Members Table
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->default('Laki-laki');
            $table->string('photo_url')->nullable();
            $table->string('access_code')->index(); // PIN atau kode unik statis
            $table->enum('status', ['active', 'inactive', 'expiring_soon'])->default('active');
            $table->date('expired_at')->nullable();
            $table->timestamps();
        });

        // 2. Products Table (Katalog POS Membership & Inventaris Ritel)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->enum('category', ['membership', 'supplement', 'drink', 'merchandise'])->default('membership');
            $table->integer('price')->default(0);
            $table->integer('stock')->default(0); // Unlimited atau jumlah stok ritel
            $table->timestamps();
        });

        // 3. POS Transactions Table
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Kasir/Staf
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->integer('total_amount')->default(0);
            $table->enum('payment_method', ['cash', 'qris', 'transfer'])->default('cash');
            $table->enum('type', ['membership', 'inventory'])->default('membership');
            $table->timestamps();
        });

        // 4. POS Transaction Items Table
        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_transaction_id')->constrained('pos_transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name');
            $table->integer('qty')->default(1);
            $table->integer('price')->default(0);
            $table->integer('subtotal')->default(0);
            $table->timestamps();
        });

        // 5. Trainers Table
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('specialization')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // 6. Gym Classes Table
        Schema::create('gym_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('trainers')->nullOnDelete();
            $table->string('name');
            $table->string('day'); // Senin, Selasa, dst.
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_capacity')->default(20);
            $table->timestamps();
        });

        // 7. Check-Ins Table
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('access_code');
            $table->timestamp('checked_in_at');
            $table->enum('check_in_method', ['code', 'manual'])->default('code');
            $table->timestamps();
        });

        // 8. Staff Logs Table (Audit Trail Internal)
        Schema::create('staff_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_logs');
        Schema::dropIfExists('check_ins');
        Schema::dropIfExists('gym_classes');
        Schema::dropIfExists('trainers');
        Schema::dropIfExists('pos_transaction_items');
        Schema::dropIfExists('pos_transactions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('members');
    }
};
