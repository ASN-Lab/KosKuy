<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KosListing extends Model
{
    protected $fillable = [
        'owner_id', 'title', 'description', 'price_per_month', 
        'gender_type', 'latitude', 'longitude', 'address', 'is_available'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(KosFacility::class, 'kos_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(KosImage::class, 'kos_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(KosRoom::class, 'kos_id');
    }

    public function conditionUpdates(): HasMany
    {
        return $this->hasMany(KosConditionUpdate::class, 'kos_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'kos_id');
    }
}
