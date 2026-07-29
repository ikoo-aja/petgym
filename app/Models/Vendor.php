<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'category',
        'address',
        'notes',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
