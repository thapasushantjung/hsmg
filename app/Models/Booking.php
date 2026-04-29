<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\CheckoutReason;
use App\Services\NepaliDateService;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'status' => 'active',
    ];

    protected $appends = ['check_in_date_bs', 'expected_check_out_date_bs', 'actual_check_out_date_bs'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'expected_check_out_date' => 'date',
            'actual_check_out_date' => 'date',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'status' => BookingStatus::class,
            'checkout_reason' => CheckoutReason::class,
            'deposit_amount' => 'decimal:2',
            'rent_amount' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bedAssignments(): HasMany
    {
        return $this->hasMany(BedAssignment::class);
    }

    /**
     * The current (active) bed assignment for this booking.
     */
    public function currentBedAssignment(): HasOne
    {
        return $this->hasOne(BedAssignment::class)
            ->whereNull('ended_at')
            ->latestOfMany('started_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TenantLog::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to only active bookings.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Active);
    }

    public function getCheckInDateBsAttribute(): ?string
    {
        return $this->check_in_date ? app(NepaliDateService::class)->toBS($this->check_in_date) : null;
    }

    public function getExpectedCheckOutDateBsAttribute(): ?string
    {
        return $this->expected_check_out_date ? app(NepaliDateService::class)->toBS($this->expected_check_out_date) : null;
    }

    public function getActualCheckOutDateBsAttribute(): ?string
    {
        return $this->actual_check_out_date ? app(NepaliDateService::class)->toBS($this->actual_check_out_date) : null;
    }
}
