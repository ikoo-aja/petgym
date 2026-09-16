<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function createMemberUser(): User
    {
        $tenant = Tenant::create([
            'name'        => 'FitLife Test Studio',
            'subdomain'   => 'fitlife-test-' . uniqid() . '.workout.id',
            'owner_name'  => 'Test Owner',
            'owner_email' => 'owner-test@fitlife.com',
            'status'      => 'active',
        ]);

        $user = User::factory()->create([
            'email'    => 'member@fitlife.com',
            'password' => 'password123',
            'role'     => 'member',
            'tenant_id' => $tenant->id,
        ]);

        Member::create([
            'tenant_id'       => $tenant->id,
            'user_id'         => $user->id,
            'name'            => 'Budi Member',
            'email'           => 'member@fitlife.com',
            'access_code'     => '881234',
            'membership_tier' => 'Basic',
            'status'          => 'active',
        ]);

        return $user;
    }

    /**
     * Halaman settings member menampilkan form ganti password dengan toggle mata.
     */
    public function test_member_settings_page_shows_password_toggles(): void
    {
        $user = $this->createMemberUser();

        $this->actingAs($user)
            ->get(route('member.settings'))
            ->assertOk()
            ->assertSee('Kata Sandi Saat Ini')
            ->assertSee('toggleCurrentPasswordBtn')
            ->assertSee('toggleNewPasswordBtn')
            ->assertSee('toggleNewPasswordConfirmBtn')
            ->assertSee('icon-eye');
    }

    /**
     * Member bisa ganti password sendiri (tanpa admin) lewat form settings.
     */
    public function test_member_can_change_own_password(): void
    {
        $user = $this->createMemberUser();

        $this->actingAs($user)
            ->post(route('member.settings.update_password'), [
                'current_password'          => 'password123',
                'new_password'              => 'passwordbaru123',
                'new_password_confirmation' => 'passwordbaru123',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('passwordbaru123', $user->fresh()->password));
    }

    /**
     * Password lama yang salah ditolak.
     */
    public function test_member_cannot_change_password_with_wrong_current_password(): void
    {
        $user = $this->createMemberUser();

        $this->actingAs($user)
            ->post(route('member.settings.update_password'), [
                'current_password'          => 'password-salah',
                'new_password'              => 'passwordbaru123',
                'new_password_confirmation' => 'passwordbaru123',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }
}