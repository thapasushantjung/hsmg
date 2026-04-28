<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['due_date_bs', 'paid_date_bs'];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getDueDateBsAttribute(): ?string
    {
        return $this->due_date ? app(\App\Services\NepaliDateService::class)->toBS($this->due_date) : null;
    }

    public function getPaidDateBsAttribute(): ?string
    {
        return $this->paid_date ? app(\App\Services\NepaliDateService::class)->toBS($this->paid_date) : null;
    }
}
