<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainerSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'member_id',
        'trainer_id',
        'session_date',
        'status', // scheduled, completed, cancelled
    ];

    protected $casts = [
        'session_date' => 'date',
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
