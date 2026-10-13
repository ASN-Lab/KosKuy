<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KosRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'kos_listing_id',
        'room_number',
        'price_per_month',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price_per_month' => 'decimal:2',
        ];
    }

    public function kosListing(): BelongsTo
    {
        return $this->belongsTo(KosListing::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function activeTenant(): HasOne
    {
        return $this->hasOne(Tenant::class)->where('status', 'active');
    }

    public function isOccupied(): bool
    {
        return $this->tenants()->where('status', 'active')->exists();
    }
}