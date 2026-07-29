<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'notes',
        'converted_to_member_id',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function convertedMember()
    {
        return $this->belongsTo(Member::class, 'converted_to_member_id');
    }
}
