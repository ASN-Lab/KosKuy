<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'kos_listing_id', 'kos_room_id', 'name', 'phone',
        'monthly_price', 'start_date', 'end_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_price' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function kosListing(): BelongsTo
    {
        return $this->belongsTo(KosListing::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(KosRoom::class, 'kos_room_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Amount already settled by this tenant for the given YYYY-MM period.
     */
    public function paidForPeriod(string $period): float
    {
        return (float) $this->payments()
            ->where('period', $period)
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Remaining balance for the period (never negative).
     */
    public function outstandingForPeriod(string $period): float
    {
        return max(0, (float) $this->monthly_price - $this->paidForPeriod($period));
    }

    public function isPaidForPeriod(string $period): bool
    {
        return $this->outstandingForPeriod($period) <= 0;
    }
}
