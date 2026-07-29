<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GymClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'trainer_id',
        'name',
        'room',
        'duration_minutes',
        'day',
        'start_time',
        'end_time',
        'max_capacity',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
