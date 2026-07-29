<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_transaction_id',
        'product_id',
        'item_name',
        'qty',
        'price',
        'subtotal',
    ];

    public function posTransaction()
    {
        return $this->belongsTo(PosTransaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
