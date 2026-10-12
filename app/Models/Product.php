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
        'cost_price',
        'stock',
        'warehouse_stock',
        'vendor_name',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function mutations()
    {
        return $this->hasMany(StockMutation::class);
    }

    public function totalStock()
    {
        return $this->stock + $this->warehouse_stock;
    }
}
