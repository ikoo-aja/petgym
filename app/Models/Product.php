<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'price',
        'stock',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
