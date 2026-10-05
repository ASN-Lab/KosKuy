<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class KosConditionUpdate extends Model
{
    protected $fillable = ['kos_id', 'subject_type', 'subject_id', 'status', 'notes', 'updated_by'];

    public function kos(): BelongsTo
    {
        return $this->belongsTo(KosListing::class, 'kos_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
