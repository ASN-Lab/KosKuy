<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KosFacility extends Model
{
    protected $fillable = ['kos_id', 'facility_name'];

    public function kos(): BelongsTo
    {
        return $this->belongsTo(KosListing::class, 'kos_id');
    }
}
