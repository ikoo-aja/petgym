<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PtBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'member_id',
        'trainer_id',
        'booking_date',
        'booking_time',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
