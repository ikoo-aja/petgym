<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassRsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'gym_class_id',
        'member_id',
        'class_date',
        'status',
        'queue_position',
    ];

    protected $casts = [
        'class_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function gymClass()
    {
        return $this->belongsTo(GymClass::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
