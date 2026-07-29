<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LockerRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'locker_id',
        'member_id',
        'rented_at',
        'returned_at',
    ];

    protected $casts = [
        'rented_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function locker()
    {
        return $this->belongsTo(Locker::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
