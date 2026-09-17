<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'plan_id',
        'plan_name',
        'status',
        'notes',
        'selected_features',
        'created_user_id',
    ];

    protected $casts = [
        'selected_features' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * Format phone to international WhatsApp standard (e.g. 0812... -> 62812...)
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string)$this->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    /**
     * Generate WhatsApp chat link with auto message template.
     */
    public function getWhatsAppUrlAttribute(): string
    {
        $phone = $this->formatted_phone;
        $name = urlencode($this->name);
        $plan = urlencode($this->plan_name);
        $message = urlencode("Halo Kak {$this->name},\n\nTerima kasih telah mendaftar sewa website gym di PetGym SaaS untuk paket *{$this->plan_name}*.\n\nSaya dari tim Superadmin PetGym ingin mengonfirmasi pendaftaran Anda. Apakah ada pertanyaan atau ingin kami bantu proses pembayarannya?\n\nSalam,\nTim PetGym SaaS");
        return "https://wa.me/{$phone}?text={$message}";
    }
}
