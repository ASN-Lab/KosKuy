<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KosFacility extends Model
{
    protected $fillable = ['kos_id', 'facility_name', 'condition_status', 'last_updated_by'];

    protected $casts = ['last_updated_by' => 'integer'];

    public function kos(): BelongsTo
    {
        return $this->belongsTo(KosListing::class, 'kos_id');
    }

    public function lastUpdater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
