<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThriftPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id', 'thrift_product_id', 'amount', 'status',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ThriftProduct::class, 'thrift_product_id');
    }
}
