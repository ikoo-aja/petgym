<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LostFound extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'item_name',
        'location_found',
        'found_at',
        'status', // tercatat, diklaim
        'claimed_by_name',
        'claimed_at',
    ];

    protected $casts = [
        'found_at' => 'date',
        'claimed_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
