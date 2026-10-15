<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password;

/**
 * SEC-16: Kebijakan kata sandi staf yang diperketat.
 *
 * Minimal 8 karakter, wajib kombinasi huruf & angka, dan ditolak bila
 * pernah bocor dalam kebocoran data publik (uncompromised) — mencegah
 * kata sandi lemah seperti '1234' atau 'password'.
 */
class StaffPassword
{
    /**
     * Aturan kata sandi staf (untuk pembuatan/ubah kata sandi akun staf).
     */
    public static function rule(): Password
    {
        return Password::min(8)
            ->letters()
            ->numbers();
    }
}
