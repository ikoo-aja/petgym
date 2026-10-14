<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\PosTransaction;
use Carbon\Carbon;

class ManagerMemberHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_manager_hanya_melihat_riwayat_transaksi_member_selama_seminggu_terakhir(): void
    {
        $manager = User::where('role', 'manager')->first();
        $this->assertNotNull($manager);

        $member = Member::where('tenant_id', $manager->tenant_id)->first();
        $this->assertNotNull($member);

        // Transaksi 3 hari yang lalu (dalam rentang seminggu)
        $txRecent = PosTransaction::create([
            'tenant_id' => $manager->tenant_id,
            'user_id' => $manager->id,
            'member_id' => $member->id,
            'invoice_number' => '#POS-RECENT-01',
            'total_amount' => 150000,
            'payment_method' => 'cash',
            'type' => 'membership',
            'void_status' => 'none',
        ]);
        $txRecent->created_at = Carbon::now()->subDays(3);
        $txRecent->save();

        // Transaksi 15 hari yang lalu (di luar rentang seminggu)
        $txOld = PosTransaction::create([
            'tenant_id' => $manager->tenant_id,
            'user_id' => $manager->id,
            'member_id' => $member->id,
            'invoice_number' => '#POS-OLD-01',
            'total_amount' => 300000,
            'payment_method' => 'cash',
            'type' => 'membership',
            'void_status' => 'none',
        ]);
        $txOld->created_at = Carbon::now()->subDays(15);
        $txOld->save();

        $response = $this->actingAs($manager)->get(route('manager.members.history', $member->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'member',
            'transactions',
        ]);

        $txIds = collect($response->json('transactions'))->pluck('id')->all();

        $this->assertContains($txRecent->id, $txIds, 'Transaksi dalam 7 hari terakhir harus muncul di riwayat.');
        $this->assertNotContains($txOld->id, $txIds, 'Transaksi lebih dari 7 hari yang lalu tidak boleh muncul di riwayat.');
    }
}
