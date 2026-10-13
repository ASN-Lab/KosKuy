<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KosListing extends Model
{
    use HasFactory;

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
        return $this->hasMany(KosRoom::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function activeTenants(): HasMany
    {
        return $this->hasMany(Tenant::class)->where('status', 'active');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
