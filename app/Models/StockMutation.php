<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'user_id',
        'type',
        'qty',
        'from_location',
        'to_location',
        'unit_cost',
        'unit_price',
        'notes',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
