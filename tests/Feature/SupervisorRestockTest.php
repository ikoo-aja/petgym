<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\StaffLog;

class SupervisorRestockTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_supervisor_can_restock_product_without_errors()
    {
        $supervisor = User::where('email', 'supervisor@fitlife.com')->first() ?: User::where('role', 'supervisor')->first();
        $this->assertNotNull($supervisor);
        $product = Product::where('tenant_id', $supervisor->tenant_id)->first();
        $this->assertNotNull($product);

        $oldStock = $product->stock;
        $oldWarehouse = $product->warehouse_stock;

        $response = $this->actingAs($supervisor)
            ->post(route('supervisor.products.restock', $product->id), [
                'qty_add' => 5,
                'vendor_name' => 'Vendor Test Otomatis',
                'cost_price' => 25000,
                'notes' => 'Unit testing restock bug fix',
            ]);

        $response->assertRedirect(route('supervisor.features', ['tab' => 'stock']));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals($oldStock + 5, $product->stock);
        $this->assertEquals($oldWarehouse = $oldWarehouse + 5, $product->warehouse_stock);

        // Verify StaffLog was created
        $staffLog = StaffLog::where('tenant_id', 1)
            ->where('user_id', $supervisor->id)
            ->where('action', 'Penerimaan Stok Vendor ke Gudang')
            ->latest()
            ->first();

        $this->assertNotNull($staffLog);
        $this->assertStringContainsString("Stok Gudang:", $staffLog->description);
        $this->assertStringContainsString("{$product->warehouse_stock} pcs", $staffLog->description);

        // Verify StockMutation was created
        $mutation = StockMutation::where('tenant_id', 1)
            ->where('product_id', $product->id)
            ->latest()
            ->first();

        $this->assertNotNull($mutation);
        $this->assertEquals(5, $mutation->qty);
        $this->assertEquals('in_vendor', $mutation->type);
    }

    public function test_supervisor_approve_and_reject_void()
    {
        $supervisor = User::where('email', 'supervisor@fitlife.com')->first() ?: User::where('role', 'supervisor')->first();
        $this->assertNotNull($supervisor);

        // Buat dummy POS transaction pending void
        $transaction = \App\Models\PosTransaction::create([
            'tenant_id' => $supervisor->tenant_id,
            'user_id' => $supervisor->id,
            'invoice_number' => 'INV-TEST-VOID-01',
            'total_amount' => 50000,
            'payment_method' => 'cash',
            'void_status' => 'pending',
            'void_reason' => 'Salah input kasir test',
            'type' => 'inventory',
        ]);

        $product = Product::where('tenant_id', $supervisor->tenant_id)->first();
        $this->assertNotNull($product);
        \App\Models\PosTransactionItem::create([
            'pos_transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'qty' => 2,
            'price' => 25000,
            'subtotal' => 50000,
        ]);

        $oldStock = $product->stock;

        // Test Approve Void
        $response = $this->actingAs($supervisor)->post(route('supervisor.void.approve', $transaction->id));
        $response->assertRedirect(route('supervisor.features', ['tab' => 'void']));
        $response->assertSessionHas('success');

        $transaction->refresh();
        $this->assertEquals('approved', $transaction->void_status);

        $product->refresh();
        $this->assertEquals($oldStock + 2, $product->stock);

        // Re-approving already approved void should be blocked
        $response2 = $this->actingAs($supervisor)->post(route('supervisor.void.approve', $transaction->id));
        $response2->assertSessionHas('error');
    }
}
