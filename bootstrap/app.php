<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);
        $middleware->redirectTo(
            guests: '/login',
            users: function ($request) {
                $user = auth()->user();
                if (!$user) return '/login';
                return route($user->dashboardRoute(), absolute: false);
            }
        );
        $middleware->web(append: [
            \App\Http\Middleware\EnsurePasswordChanged::class,
            \App\Http\Middleware\EnsureTenantSetup::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserRole::class,
            'tenant.setup' => \App\Http\Middleware\EnsureTenantSetup::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 419 || $e instanceof \Illuminate\Session\TokenMismatchException) {
                if ($request->is('logout')) {
                    \Illuminate\Support\Facades\Auth::logout();
                    if ($request->hasSession()) {
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    }
                    return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
                }

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Sesi formulir telah berakhir, silakan muat ulang halaman.'], 419);
                }

                return redirect()->back()
                    ->withInput($request->except('_token', 'password', 'password_confirmation'))
                    ->with('warning', 'Sesi formulir telah diperbarui karena token kedaluwarsa. Silakan tekan tombol Simpan sekali lagi.');
            }

            return $response;
        });

        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->is('logout')) {
                \Illuminate\Support\Facades\Auth::logout();
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
                return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi formulir telah berakhir, silakan muat ulang halaman.'], 419);
            }

            return redirect()->back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('warning', 'Sesi formulir telah diperbarui karena token kedaluwarsa. Silakan tekan tombol Simpan sekali lagi.');
        });
    })->create();
