<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Locker extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'locker_number',
        'status', // tersedia, terpakai, rusak
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rentals()
    {
        return $this->hasMany(LockerRental::class);
    }

    public function activeRental()
    {
        return $this->hasOne(LockerRental::class)->whereNull('returned_at');
    }
}
