<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GymEquipment extends Model
{
    protected $table = 'gym_equipments';

    protected $fillable = [
        'tenant_id', 'name', 'category', 'brand', 'status',
        'purchase_date', 'next_service_date', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'next_service_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(EquipmentMaintenanceLog::class);
    }
}
