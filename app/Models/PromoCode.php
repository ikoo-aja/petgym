<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isValid()
    {
        $today = now()->startOfDay();
        return $this->is_active &&
            $today->gte($this->valid_from->startOfDay()) &&
            $today->lte($this->valid_until->endOfDay()) &&
            $this->used_count < $this->max_uses;
    }
}
