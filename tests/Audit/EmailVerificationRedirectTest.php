<?php

namespace Tests\Audit;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationRedirectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_verifikasi_email_admin_redirect_ke_login_dan_tidak_auto_login(): void
    {
        $admin = User::where('email', 'admin@fitlife.com')->first();
        $this->assertNotNull($admin);

        // Simulasi unverified
        $admin->email_verified_at = null;
        $admin->save();

        // Buat signed verification url
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $admin->id, 'hash' => sha1($admin->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Pastikan tidak auto-login
        $this->assertFalse(Auth::check());

        // Pastikan email telah terverifikasi di database
        $this->assertTrue($admin->fresh()->hasVerifiedEmail());
    }

    public function test_verifikasi_email_member_redirect_ke_login_tenant_dan_tidak_auto_login(): void
    {
        $member = User::where('role', 'member')->first();
        $this->assertNotNull($member);

        $member->email_verified_at = null;
        $member->save();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $member->id, 'hash' => sha1($member->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(302);
        $tenantLoginUrl = $member->tenant ? $member->tenant->publicLandingUrl() . '/login' : route('member.login');
        $response->assertRedirect($tenantLoginUrl);
        $response->assertSessionHas('success');

        // Pastikan tidak login
        $this->assertFalse(Auth::check());
        $this->assertTrue($member->fresh()->hasVerifiedEmail());
    }
}
