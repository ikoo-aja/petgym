<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipmentMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'gym_equipment_id',
        'action',
        'description',
        'cost',
        'serviced_at',
        'next_service_date',
    ];

    protected $casts = [
        'serviced_at' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function equipment()
    {
        return $this->belongsTo(GymEquipment::class, 'gym_equipment_id');
    }
}
