<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    /**
     * Ambil nilai pengaturan berdasarkan key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if ($setting === null || $setting->value === null) {
            return $default;
        }

        return $setting->value;
    }

    /**
     * Simpan atau perbarui nilai pengaturan berdasarkan key.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return static
     */
    public static function set(string $key, $value, string $group = 'general'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'value' => $value !== null ? (string) $value : null,
            ]
        );
    }

    /**
     * Ambil semua pengaturan dalam satu kelompok (group) sebagai array key => value.
     *
     * @param string $group
     * @return array<string, mixed>
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
