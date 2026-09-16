<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginRoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    public static function rolesAndDashboards(): array
    {
        return [
            'superadmin'   => ['superadmin', '/superadmin/dashboard'],
            'owner'        => ['owner', '/owner/dashboard'],
            'admin'        => ['admin', '/admin/dashboard'],
            'manager'      => ['manager', '/manager/dashboard'],
            'receptionist' => ['receptionist', '/receptionist/dashboard'],
            'trainer'      => ['trainer', '/trainer/dashboard'],
            'member'       => ['member', '/member/dashboard'],
        ];
    }

    /**
     * Setelah login, setiap role diarahkan ke dashboard miliknya sendiri.
     */
    #[DataProvider('rolesAndDashboards')]
    public function test_each_role_is_redirected_to_its_own_dashboard(string $role, string $dashboard): void
    {
        $user = User::factory()->create([
            'email'    => $role . '@role-test.com',
            'password' => 'password123',
            'role'     => $role,
        ]);

        $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'password123',
        ])->assertRedirect($dashboard);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Login dengan password salah tidak pernah masuk (dan tidak redirect dashboard).
     */
    public function test_login_with_wrong_password_stays_on_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@fitlife.com',
            'password' => 'password123',
            'role'     => 'admin',
        ]);

        $this->post(route('login'), [
            'email'    => 'admin@fitlife.com',
            'password' => 'password-salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
