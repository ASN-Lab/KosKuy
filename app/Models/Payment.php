<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    public const METHOD_TRANSFER = 'transfer_bank';
    public const METHOD_CASH = 'cash_on_pickup';

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const STATUS_PENDING_PICKUP = 'pending_pickup';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'payable_type',
        'payable_id',
        'method',
        'amount',
        'unique_code',
        'proof_path',
        'status',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Stamp verifier whenever a payment reaches a verified terminal state,
        // regardless of the entry point (API upload, Filament modal, tinker).
        static::updating(function (Payment $payment): void {
            if ($payment->isDirty('status')
                && in_array($payment->status, [self::STATUS_PAID, self::STATUS_REJECTED], true)
                && $payment->verified_at === null) {
                $payment->verified_by = auth()->id();
                $payment->verified_at = now();
            }
        });
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}