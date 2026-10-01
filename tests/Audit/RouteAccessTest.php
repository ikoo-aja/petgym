<?php

namespace Tests\Audit;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/**
 * AUDIT AKSES HALAMAN (GET)
 *
 * Tujuan:
 *  1. Memastikan setiap halaman milik sebuah role dapat dibuka (tidak 500 / tidak 403).
 *  2. Memastikan setiap role TIDAK bisa membuka halaman milik role lain (harus 403).
 *  3. Memastikan tamu (belum login) diarahkan ke halaman login.
 */
class RouteAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    /**
     * Daftar halaman (route GET) yang semestinya bisa dibuka tiap role.
     *
     * @return array<string, array<int, string>>
     */
    private function ownPages(): array
    {
        return [
            'superadmin@petgym.com' => [
                'superadmin.dashboard', 'superadmin.registrations', 'superadmin.tenants',
                'superadmin.plans', 'superadmin.billing', 'superadmin.announcements',
                'superadmin.logs', 'superadmin.settings', 'superadmin.profile',
            ],
            'admin@fitlife.com' => [
                'admin.dashboard', 'admin.staff.index', 'admin.landing.edit',
                'admin.subscription.index', 'admin.settings.index',
            ],
            'manager@fitlife.com' => [
                'manager.dashboard', 'manager.features', 'manager.members.index',
                'manager.classes.index', 'manager.staff.index',
            ],
            'supervisor@fitlife.com' => [
                'supervisor.dashboard', 'supervisor.features',
            ],
            'resepsionis@fitlife.com' => [
                'receptionist.shifts', 'receptionist.dashboard', 'receptionist.pos.index',
                'receptionist.checkin.index', 'receptionist.lockers.index', 'receptionist.lockers',
                'receptionist.lost-found', 'receptionist.complaints',
            ],
            'alex@fitlife.com' => [
                'trainer.dashboard', 'trainer.pt-sessions', 'trainer.classes', 'trainer.rsvps',
            ],
            'owner@fitlife.com' => [
                'owner.dashboard', 'owner.transactions', 'owner.performance', 'owner.classes',
                'owner.inventory', 'owner.staff', 'owner.logs', 'owner.reports', 'owner.settings',
            ],
            'member@fitlife.com' => [
                'member.dashboard', 'member.lockers', 'member.membership', 'member.pt',
                'member.classes', 'member.billing', 'member.guide', 'member.settings',
            ],
        ];
    }

    public function test_halaman_publik_dapat_dibuka(): void
    {
        $uris = ['/', '/login', '/register', '/member/login', '/member/register', '/forgot-password'];
        $failures = [];

        foreach ($uris as $uri) {
            $status = $this->get($uri)->getStatusCode();
            if ($status !== 200) {
                $failures[] = "GET {$uri} => HTTP {$status}";
            }
        }

        $this->assertSame([], $failures, "Halaman publik bermasalah:\n" . implode("\n", $failures));
    }

    public function test_halaman_milik_role_tidak_error_dan_tidak_terlarang(): void
    {
        $failures = [];

        foreach ($this->ownPages() as $email => $routes) {
            $user = $this->user($email);

            foreach ($routes as $name) {
                $response = $this->actingAs($user)->get(route($name));
                $status = $response->getStatusCode();

                if ($status >= 500) {
                    $failures[] = "[{$email}] GET {$name} => HTTP {$status} (SERVER ERROR)";
                } elseif ($status === 403) {
                    $failures[] = "[{$email}] GET {$name} => HTTP 403 (role sendiri malah dilarang)";
                }
            }
        }

        $this->assertSame([], $failures, "Masalah pada halaman milik role:\n" . implode("\n", $failures));
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $routes = [
            'account.settings', 'admin.dashboard', 'superadmin.dashboard', 'manager.dashboard',
            'supervisor.dashboard', 'receptionist.shifts', 'trainer.dashboard',
            'owner.dashboard', 'member.dashboard',
        ];
        $failures = [];

        foreach ($routes as $name) {
            $response = $this->get(route($name));
            $status = $response->getStatusCode();
            $location = (string) $response->headers->get('Location');

            if ($status !== 302 || !str_contains($location, '/login')) {
                $failures[] = "Guest GET {$name} => HTTP {$status} (Location: {$location})";
            }
        }

        $this->assertSame([], $failures, "Proteksi tamu bermasalah:\n" . implode("\n", $failures));
    }

    /**
     * Hirarki role: role yang satu TIDAK boleh menembus area role lain.
     */
    public function test_hirarki_role_menolak_akses_lintas_area(): void
    {
        // [email, route yang HARUS boleh (bukan 403), route yang HARUS 403]
        $matrix = [
            ['superadmin@petgym.com',
                ['superadmin.dashboard'],
                ['admin.dashboard', 'manager.dashboard', 'supervisor.dashboard', 'receptionist.shifts', 'trainer.dashboard', 'owner.dashboard', 'member.dashboard']],
            ['admin@fitlife.com',
                ['admin.dashboard'],
                ['superadmin.dashboard', 'manager.dashboard', 'supervisor.dashboard', 'receptionist.shifts', 'trainer.dashboard', 'owner.dashboard', 'member.dashboard']],
            ['manager@fitlife.com',
                ['manager.dashboard', 'receptionist.dashboard', 'manager.members.index'],
                ['superadmin.dashboard', 'admin.dashboard', 'supervisor.dashboard', 'trainer.dashboard', 'owner.dashboard', 'member.dashboard']],
            ['supervisor@fitlife.com',
                ['supervisor.dashboard'],
                ['superadmin.dashboard', 'admin.dashboard', 'manager.dashboard', 'receptionist.shifts', 'trainer.dashboard', 'owner.dashboard', 'member.dashboard']],
            ['resepsionis@fitlife.com',
                ['receptionist.shifts', 'manager.members.index', 'manager.classes.index'],
                ['superadmin.dashboard', 'admin.dashboard', 'manager.dashboard', 'supervisor.dashboard', 'trainer.dashboard', 'owner.dashboard', 'member.dashboard']],
            ['alex@fitlife.com',
                ['trainer.dashboard', 'manager.members.index', 'manager.classes.index'],
                ['superadmin.dashboard', 'admin.dashboard', 'manager.dashboard', 'supervisor.dashboard', 'receptionist.shifts', 'owner.dashboard', 'member.dashboard']],
            ['owner@fitlife.com',
                ['owner.dashboard'],
                ['superadmin.dashboard', 'admin.dashboard', 'manager.dashboard', 'supervisor.dashboard', 'receptionist.shifts', 'trainer.dashboard', 'member.dashboard']],
            ['member@fitlife.com',
                ['member.dashboard'],
                ['superadmin.dashboard', 'admin.dashboard', 'manager.dashboard', 'supervisor.dashboard', 'receptionist.shifts', 'trainer.dashboard', 'owner.dashboard']],
        ];

        $failures = [];

        foreach ($matrix as [$email, $allowed, $forbidden]) {
            $user = $this->user($email);

            foreach ($allowed as $name) {
                $status = $this->actingAs($user)->get(route($name))->getStatusCode();
                if ($status === 403) {
                    $failures[] = "[{$email}] seharusnya BOLEH GET {$name}, tetapi dapat 403";
                }
            }

            foreach ($forbidden as $name) {
                $status = $this->actingAs($user)->get(route($name))->getStatusCode();
                if ($status !== 403) {
                    $failures[] = "[{$email}] dapat menembus GET {$name} (HTTP {$status}, harusnya 403)";
                }
            }
        }

        $this->assertSame([], $failures, "Pelanggaran hirarki role:\n" . implode("\n", $failures));
    }
}
