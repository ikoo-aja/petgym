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
        // 1. Tambahkan kolom stok gudang, harga modal vendor, dan nama vendor pada tabel products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'warehouse_stock')) {
                $table->integer('warehouse_stock')->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->integer('cost_price')->default(0)->after('warehouse_stock');
            }
            if (!Schema::hasColumn('products', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('cost_price');
            }
        });

        // 2. Buat tabel stock_mutations untuk audit trail & laporan mutasi stok
        if (!Schema::hasTable('stock_mutations')) {
            Schema::create('stock_mutations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('type', ['in_vendor', 'transfer_to_display', 'sale_pos', 'adjustment'])->default('in_vendor');
                $table->integer('qty')->default(1);
                $table->string('from_location')->default('vendor'); // vendor, gudang, etalase
                $table->string('to_location')->default('gudang');   // gudang, etalase, customer
                $table->integer('unit_cost')->default(0);
                $table->integer('unit_price')->default(0);
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'vendor_name')) {
                $table->dropColumn('vendor_name');
            }
            if (Schema::hasColumn('products', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
            if (Schema::hasColumn('products', 'warehouse_stock')) {
                $table->dropColumn('warehouse_stock');
            }
        });
    }
};
