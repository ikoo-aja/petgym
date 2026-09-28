<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'max_members',
        'features',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Batas kuota member aktif.
     * Basic: 150, Pro: 500, Enterprise: null (unlimited)
     */
    public function getMaxMembersLimit(): ?int
    {
        if ($this->max_members !== null) {
            return (int) $this->max_members;
        }

        return match ($this->name) {
            'Paket Basic' => 150,
            'Paket Pro' => 500,
            'Paket Enterprise' => null,
            default => 150,
        };
    }

    /**
     * Batas kuota akun staf/karyawan.
     * Basic: 5, Pro: 15, Enterprise: null (unlimited)
     */
    public function getMaxStaffLimit(): ?int
    {
        return match ($this->name) {
            'Paket Basic' => 5,
            'Paket Pro' => 15,
            'Paket Enterprise' => null,
            default => 5,
        };
    }
}
