<?php

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['full_name', 'joined_date_bs', 'date_of_birth_bs'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joined_date' => 'date',
            'security_deposit' => 'decimal:2',
            'monthly_rent_agreed' => 'decimal:2',
        ];
    }

    /**
     * Get the tenant's full name.
     */
    public function getFullNameAttribute(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }

    public function getJoinedDateBsAttribute(): ?string
    {
        return $this->joined_date ? app(\App\Services\NepaliDateService::class)->toBS($this->joined_date) : null;
    }

    public function getDateOfBirthBsAttribute(): ?string
    {
        return $this->date_of_birth ? app(\App\Services\NepaliDateService::class)->toBS($this->date_of_birth) : null;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
