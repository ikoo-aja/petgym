<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPasswordResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'target_type',
        'email',
        'admin_name',
        'user_role',
        'token',
        'status',
        'reason',
        'notes',
        'approved_by',
        'approved_at',
        'token_expires_at',
        'ip_address',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'token_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }
}
