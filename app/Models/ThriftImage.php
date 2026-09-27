<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThriftImage extends Model
{
    protected $fillable = ['thrift_id', 'image_path'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ThriftProduct::class, 'thrift_id');
    }
}
