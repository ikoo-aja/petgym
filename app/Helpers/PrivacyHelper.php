<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PrivacyHelper
{
    /**
     * Mask email address for privacy (e.g. budi@gmail.com -> b***@gmail.com)
     */
    public static function maskEmail(?string $email): string
    {
        if (empty($email)) {
            return '-';
        }

        $user = Auth::user();
        if (!$user || !$user->isOwner()) {
            return $email;
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***';
        }

        $name = $parts[0];
        $domain = $parts[1];

        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '***';
        } else {
            $maskedName = substr($name, 0, 1) . str_repeat('*', max(3, $len - 2)) . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Mask phone number for privacy (e.g. 081234567890 -> 0812****7890)
     */
    public static function maskPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '-';
        }

        $user = Auth::user();
        if (!$user || !$user->isOwner()) {
            return $phone;
        }

        $len = strlen($phone);
        if ($len <= 6) {
            return substr($phone, 0, 2) . '****';
        }

        $prefix = substr($phone, 0, 4);
        $suffix = substr($phone, -4);
        $maskedLength = max(4, $len - 8);

        return $prefix . str_repeat('*', $maskedLength) . $suffix;
    }

    /**
     * Mask PIN / access code for privacy (e.g. 881234 -> 88****)
     */
    public static function maskCode(?string $code): string
    {
        if (empty($code)) {
            return '-';
        }

        $user = Auth::user();
        if (!$user || !$user->isOwner()) {
            return $code;
        }

        $len = strlen($code);
        if ($len <= 2) {
            return '**';
        }

        return substr($code, 0, 2) . str_repeat('*', max(4, $len - 2));
    }
}
