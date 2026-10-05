<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KosRoom extends Model
{
    protected $fillable = ['kos_id', 'room_name', 'is_occupied', 'tenant_name'];

    protected $casts = ['is_occupied' => 'boolean'];

    public function kos(): BelongsTo
    {
        return $this->belongsTo(KosListing::class, 'kos_id');
    }
}
