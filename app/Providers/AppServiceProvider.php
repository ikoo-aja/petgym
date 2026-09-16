<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        \Illuminate\Support\Facades\View::composer(['member.*', 'layouts.member'], function ($view) {
            $user = auth()->user();
            $tenantName = ($user && $user->tenant) ? $user->tenant->name : 'Gym Portal';
            $view->with('tenantName', $tenantName);
        });

        VerifyEmail::toMailUsing(function ($notifiable, $verificationUrl) {
            /** @var \App\Models\User $notifiable */
            $tenant = $notifiable->tenant;

            if ($notifiable->role === 'member' && $tenant) {
                return (new MailMessage)
                    ->subject("Verifikasi Email Keanggotaan — {$tenant->name}")
                    ->greeting("Halo, {$notifiable->name}!")
                    ->line("Terima kasih telah mendaftar sebagai member di {$tenant->name}.")
                    ->line("Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akun member Anda.")
                    ->action('Verifikasi Email Member', $verificationUrl)
                    ->line("Jika Anda tidak merasa mendaftar di {$tenant->name}, silakan abaikan email ini.")
                    ->salutation("Salam hangat,\nTim {$tenant->name}");
            }

            $appName = config('app.name', 'PetGym');
            return (new MailMessage)
                ->subject("Verifikasi Email Akun — {$appName}")
                ->greeting("Halo, {$notifiable->name}!")
                ->line("Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.")
                ->action('Verifikasi Alamat Email', $verificationUrl)
                ->line("Jika Anda tidak merasa membuat akun, abaikan email ini.")
                ->salutation("Salam hangat,\n{$appName}");
        });
    }
}
