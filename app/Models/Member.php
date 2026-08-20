<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'photo_url',
        'access_code',
        'membership_tier',
        'status',
        'no_show_count',
        'penalty_blocked_until',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'date',
        'penalty_blocked_until' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posTransactions()
    {
        return $this->hasMany(PosTransaction::class);
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function lockerRentals()
    {
        return $this->hasMany(LockerRental::class);
    }

    public function ptQuotas()
    {
        return $this->hasMany(MemberPtQuota::class);
    }

    public function ptBookings()
    {
        return $this->hasMany(PtBooking::class);
    }

    public function classRsvps()
    {
        return $this->hasMany(ClassRsvp::class);
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

    public function getIsPenaltyBlockedAttribute(): bool
    {
        return $this->penalty_blocked_until && $this->penalty_blocked_until->isFuture();
    }
}
