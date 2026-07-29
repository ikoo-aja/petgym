<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'photo_url',
        'access_code',
        'status',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function posTransactions()
    {
        return $this->hasMany(PosTransaction::class);
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    public function getDaysLeftAttribute(): int
    {
        if (!$this->expired_at || $this->expired_at->isPast()) {
            return 0;
        }
        return (int) now()->diffInDays($this->expired_at, false);
    }
}
