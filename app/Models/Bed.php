<?php

namespace App\Models;

use Database\Factories\BedFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bed extends Model
{
    /** @use HasFactory<BedFactory> */
    use HasFactory;

    protected $guarded = [];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bedAssignments(): HasMany
    {
        return $this->hasMany(BedAssignment::class);
    }

    /**
     * The current (active) bed assignment — null means the bed is free.
     */
    public function currentAssignment(): HasOne
    {
        return $this->hasOne(BedAssignment::class)
            ->whereNull('ended_at')
            ->latestOfMany('started_at');
    }
}
