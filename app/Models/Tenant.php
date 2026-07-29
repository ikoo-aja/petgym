<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subdomain',
        'owner_name',
        'owner_email',
        'plan_id',
        'plan_name',
        'status',
        'joined_at',
        'expires_at',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'joined_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getExpiresInDaysAttribute()
    {
        if (!$this->expires_at || $this->status === 'suspended') {
            return 0;
        }

        $days = (int) now()->diffInDays($this->expires_at, false);
        return max(0, $days);
    }
}
