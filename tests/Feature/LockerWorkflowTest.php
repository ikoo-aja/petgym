<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Locker;
use App\Models\Member;
use App\Models\StaffLog;
use App\Models\ReceptionistShift;

class LockerWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_manager_can_create_and_bulk_create_lockers()
    {
        $manager = User::where('role', 'manager')->first();
        $this->assertNotNull($manager);

        // 1. Manager views locker tab
        $response = $this->actingAs($manager)->get(route('manager.features', ['tab' => 'locker']));
        $response->assertStatus(200);
        $response->assertSee('Master Loker Gym');

        // 2. Manager adds single locker
        $uniqueNum = 'MGR-99';
        Locker::where('tenant_id', $manager->tenant_id)->where('locker_number', $uniqueNum)->delete();

        $postRes = $this->actingAs($manager)->post(route('manager.lockers.store'), [
            'locker_number' => $uniqueNum,
            'status' => 'tersedia',
        ]);
        $postRes->assertRedirect(route('manager.features', ['tab' => 'locker']));
        $this->assertDatabaseHas('lockers', [
            'tenant_id' => $manager->tenant_id,
            'locker_number' => $uniqueNum,
            'status' => 'tersedia',
        ]);

        // 3. Manager bulk creates lockers by quantity
        $bulkRes = $this->actingAs($manager)->post(route('manager.lockers.bulk'), [
            'mode' => 'quantity',
            'quantity' => 3,
            'prefix' => 'BK',
        ]);
        $bulkRes->assertRedirect(route('manager.features', ['tab' => 'locker']));

        $this->assertDatabaseHas('lockers', [
            'tenant_id' => $manager->tenant_id,
            'locker_number' => 'BK-01',
            'status' => 'tersedia',
        ]);

        // 4. Manager can delete non-occupied locker
        $lockerToDelete = Locker::where('tenant_id', $manager->tenant_id)->where('locker_number', $uniqueNum)->first();
        $delRes = $this->actingAs($manager)->delete(route('manager.lockers.destroy', $lockerToDelete->id));
        $delRes->assertRedirect(route('manager.features', ['tab' => 'locker']));
        $this->assertDatabaseMissing('lockers', ['id' => $lockerToDelete->id]);
    }

    public function test_receptionist_can_report_broken_and_supervisor_can_repair()
    {
        $receptionist = User::where('role', 'receptionist')->first();
        $supervisor = User::where('role', 'supervisor')->where('tenant_id', $receptionist->tenant_id)->first();
        $member = Member::where('tenant_id', $receptionist->tenant_id)->first();

        $this->assertNotNull($receptionist);
        $this->assertNotNull($supervisor);
        $this->assertNotNull($member);

        // Ensure receptionist has active shift
        ReceptionistShift::firstOrCreate(
            ['tenant_id' => $receptionist->tenant_id, 'user_id' => $receptionist->id, 'status' => 'open'],
            ['start_cash' => 100000, 'opened_at' => now()]
        );

        $testLocker = Locker::create([
            'tenant_id' => $receptionist->tenant_id,
            'locker_number' => 'TEST-01',
            'status' => 'tersedia',
        ]);

        // 1. Receptionist reports available locker as damaged
        $reportRes = $this->actingAs($receptionist)->post(route('receptionist.lockers.report-damage', $testLocker->id), [
            'notes' => 'Kunci patah di dalam lubang kunci',
        ]);
        $reportRes->assertRedirect(route('receptionist.lockers'));

        $testLocker->refresh();
        $this->assertEquals('rusak', $testLocker->status);
        $this->assertEquals('Kunci patah di dalam lubang kunci', $testLocker->notes);

        // 2. Supervisor checks features tab locker and sees broken locker
        $supViewRes = $this->actingAs($supervisor)->get(route('supervisor.features', ['tab' => 'locker']));
        $supViewRes->assertStatus(200);
        $supViewRes->assertSee('TEST-01');
        $supViewRes->assertSee('Kunci patah di dalam lubang kunci');

        // 3. Supervisor executes repair
        $repairRes = $this->actingAs($supervisor)->post(route('supervisor.lockers.repair', $testLocker->id), [
            'repair_notes' => 'Silinder kunci telah diganti dengan yang baru',
        ]);
        $repairRes->assertRedirect(route('supervisor.features', ['tab' => 'locker']));

        $testLocker->refresh();
        $this->assertEquals('tersedia', $testLocker->status);
        $this->assertNull($testLocker->notes);

        // Verify StaffLog
        $log = StaffLog::where('tenant_id', $supervisor->tenant_id)
            ->where('user_id', $supervisor->id)
            ->where('action', 'Perbaikan Loker Selesai')
            ->latest()
            ->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('TEST-01', $log->description);
    }

    public function test_receptionist_can_return_locker_with_damaged_condition()
    {
        $receptionist = User::where('role', 'receptionist')->first();
        $member = Member::where('tenant_id', $receptionist->tenant_id)->first();

        // Ensure receptionist has active shift
        ReceptionistShift::firstOrCreate(
            ['tenant_id' => $receptionist->tenant_id, 'user_id' => $receptionist->id, 'status' => 'open'],
            ['start_cash' => 100000, 'opened_at' => now()]
        );

        $locker = Locker::create([
            'tenant_id' => $receptionist->tenant_id,
            'locker_number' => 'TEST-RET',
            'status' => 'tersedia',
        ]);

        // Assign locker
        $this->actingAs($receptionist)->post(route('receptionist.lockers.assign'), [
            'locker_id' => $locker->id,
            'member_id' => $member->id,
        ]);
        $locker->refresh();
        $this->assertEquals('terpakai', $locker->status);

        // Return locker as broken
        $returnRes = $this->actingAs($receptionist)->post(route('receptionist.lockers.return', $locker->id), [
            'condition' => 'rusak',
            'notes' => 'Engsel pintu copot saat dipakai member',
        ]);
        $returnRes->assertRedirect(route('receptionist.lockers'));

        $locker->refresh();
        $this->assertEquals('rusak', $locker->status);
        $this->assertEquals('Engsel pintu copot saat dipakai member', $locker->notes);
    }

    public function test_receptionist_can_view_lockers_page()
    {
        $receptionist = User::where('role', 'receptionist')->first();
        $this->assertNotNull($receptionist);

        ReceptionistShift::firstOrCreate(
            ['tenant_id' => $receptionist->tenant_id, 'user_id' => $receptionist->id, 'status' => 'open'],
            ['start_cash' => 100000, 'opened_at' => now()]
        );

        Locker::create([
            'tenant_id' => $receptionist->tenant_id,
            'locker_number' => 'V-01',
            'status' => 'tersedia',
        ]);

        Locker::create([
            'tenant_id' => $receptionist->tenant_id,
            'locker_number' => 'V-02',
            'status' => 'terpakai',
        ]);

        Locker::create([
            'tenant_id' => $receptionist->tenant_id,
            'locker_number' => 'V-03',
            'status' => 'rusak',
            'notes' => 'Kunci rusak',
        ]);

        $response = $this->actingAs($receptionist)->get(route('receptionist.lockers'));
        $response->assertStatus(200);
        $response->assertSee('Peta Loker Gym');
        $response->assertSee('V-01');
        $response->assertSee('V-02');
        $response->assertSee('V-03');
    }
}
