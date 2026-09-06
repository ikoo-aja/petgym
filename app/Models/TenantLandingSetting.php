<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantLandingSetting extends Model
{
    protected $fillable = [
        'tenant_id',
        'template',
        'hero_title',
        'hero_tagline',
        'about_text',
        'address',
        'phone',
        'email',
        'instagram',
        'facebook',
        'opening_hours',
        'cta_text',
        'cta_url',
        'primary_color',
        'secondary_color',
        'features',
        'stats',
        'sections_enabled',
    ];

    protected $casts = [
        'features'        => 'array',
        'stats'           => 'array',
        'sections_enabled'=> 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Bagian default yang selalu tampil (hero, about, features, contact, footer).
     */
    public static function defaultSections(): array
    {
        return ['hero', 'about', 'features', 'contact', 'footer'];
    }
}
