<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subdomain',
        'slug',
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

    protected static function booted(): void
    {
        // Otomatis isi slug dari label pertama subdomain saat create/update
        static::saving(function (Tenant $tenant) {
            if (empty($tenant->slug) && $tenant->subdomain) {
                $tenant->slug = Str::before($tenant->subdomain, '.');
            }
        });
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function landing()
    {
        return $this->hasOne(TenantLandingSetting::class);
    }

    /**
     * Mengambil (atau membuat) pengaturan landing page tenant ini.
     */
    public function landingSettings(): TenantLandingSetting
    {
        if ($this->landing) {
            return $this->landing;
        }

        $settings = $this->landing()->create([
            'template'          => 'default',
            'hero_title'        => $this->name,
            'hero_tagline'      => "Selamat datang di {$this->name} — partner kebugaran Anda.",
            'about_text'        => "{$this->name} adalah pusat kebugaran modern yang siap membantu Anda mencapai target kesehatan dan kebugaran.",
            'opening_hours'     => 'Senin - Minggu: 06.00 - 22.00',
            'cta_text'          => 'Mulai Sekarang',
            'cta_url'           => config('app.url'),
            'primary_color'     => '#f43f5e',
            'secondary_color'   => '#111827',
            'sections_enabled'  => TenantLandingSetting::defaultSections(),
        ]);

        $this->setRelation('landing', $settings);

        return $settings;
    }

    /**
     * Kemampuan kustomisasi landing page berdasarkan paket langganan.
     *  - Semua paket : teks dasar (judul, tagline, tentang, kontak, CTA)
     *  - Pro        : + warna brand, toggle bagian halaman, daftar fitur unggulan
     *  - Enterprise : + statistik angka
     */
    public function landingCapabilities(): array
    {
        return match ($this->plan_name) {
            'Paket Enterprise' => ['text', 'colors', 'sections', 'features', 'stats'],
            'Paket Pro'        => ['text', 'colors', 'sections', 'features'],
            default            => ['text'],
        };
    }

    /**
     * Apakah tenant pada paket ini boleh mengedit capability tertentu?
     */
    public function canLanding(string $capability): bool
    {
        return in_array($capability, $this->landingCapabilities(), true);
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
