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

    public function classRsvps()
    {
        return $this->hasMany(ClassRsvp::class);
    }

    public function getTimeAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
        }
        return substr($this->start_time, 0, 5) ?: '-';
    }

    public function getCapacityAttribute()
    {
        return $this->max_capacity ?? 20;
    }
}
