<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['check_in_date_bs', 'expected_check_out_date_bs', 'actual_check_out_date_bs'];

    protected $casts = [
        'check_in_date' => 'date',
        'expected_check_out_date' => 'date',
        'actual_check_out_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getCheckInDateBsAttribute(): ?string
    {
        return $this->check_in_date ? app(\App\Services\NepaliDateService::class)->toBS($this->check_in_date) : null;
    }

    public function getExpectedCheckOutDateBsAttribute(): ?string
    {
        return $this->expected_check_out_date ? app(\App\Services\NepaliDateService::class)->toBS($this->expected_check_out_date) : null;
    }

    public function getActualCheckOutDateBsAttribute(): ?string
    {
        return $this->actual_check_out_date ? app(\App\Services\NepaliDateService::class)->toBS($this->actual_check_out_date) : null;
    }
}
