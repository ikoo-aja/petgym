<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subdomain',
        'owner_name',
        'owner_email',
        'plan_id',
        'plan_name',
        'status',
        'joined_at',
        'expires_at',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'joined_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function managers()
    {
        return $this->hasMany(Manager::class);
    }

    public function receptionists()
    {
        return $this->hasMany(Receptionist::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function posTransactions()
    {
        return $this->hasMany(PosTransaction::class);
    }

    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }

    public function gymClasses()
    {
        return $this->hasMany(GymClass::class);
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function staffLogs()
    {
        return $this->hasMany(StaffLog::class);
    }

    public function gymEquipments()
    {
        return $this->hasMany(GymEquipment::class);
    }

    public function staffShifts()
    {
        return $this->hasMany(StaffShift::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function promoCodes()
    {
        return $this->hasMany(PromoCode::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    public function lockers()
    {
        return $this->hasMany(Locker::class);
    }

    public function lockerRentals()
    {
        return $this->hasMany(LockerRental::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function lostFounds()
    {
        return $this->hasMany(LostFound::class);
    }

    public function receptionistShifts()
    {
        return $this->hasMany(ReceptionistShift::class);
    }

    public function trainerSessions()
    {
        return $this->hasMany(TrainerSession::class);
    }

    public function getExpiresInDaysAttribute()
    {
        if (!$this->expires_at || $this->status === 'suspended') {
            return 0;
        }

        $days = (int) now()->diffInDays($this->expires_at, false);
        return max(0, $days);
    }
}
