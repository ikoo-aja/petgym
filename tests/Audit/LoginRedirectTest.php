<?php

namespace Tests\Audit;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * AUDIT REDIRECT SETELAH LOGIN
 *
 * Setiap role harus diarahkan ke dashboard miliknya sendiri.
 */
class LoginRedirectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_mengarahkan_ke_dashboard_sesuai_role(): void
    {
        $cases = [
            'superadmin@petgym.com' => 'superadmin/dashboard',
            'admin@fitlife.com' => 'admin/dashboard',
            'owner@fitlife.com' => 'owner/dashboard',
            'manager@fitlife.com' => 'manager/dashboard',
            'supervisor@fitlife.com' => 'supervisor/dashboard',
            'resepsionis@fitlife.com' => 'receptionist/shifts',
            'alex@fitlife.com' => 'trainer/dashboard',
            'member@fitlife.com' => 'member/dashboard',
        ];

        $failures = [];

        foreach ($cases as $email => $expected) {
            Auth::logout();
            $this->flushSession();

            $response = $this->post('/login', ['email' => $email, 'password' => '1234']);
            $location = (string) $response->headers->get('Location');

            if ($response->getStatusCode() !== 302) {
                $failures[] = "{$email} => HTTP {$response->getStatusCode()} (bukan redirect)";
            } elseif (!str_contains($location, $expected)) {
                $failures[] = "{$email} => {$location} (harusnya berisi '{$expected}')";
            }
        }

        Auth::logout();

        $this->assertSame([], $failures, "Redirect login tidak sesuai role:\n" . implode("\n", $failures));
    }

    public function test_login_supervisor_tidak_berakhir_di_area_member(): void
    {
        Auth::logout();
        $this->flushSession();

        $response = $this->post('/login', ['email' => 'supervisor@fitlife.com', 'password' => '1234']);
        $location = (string) $response->headers->get('Location');

        $this->assertStringNotContainsString(
            '/member',
            $location,
            "Supervisor diarahkan ke area member setelah login: {$location}"
        );
    }
}
