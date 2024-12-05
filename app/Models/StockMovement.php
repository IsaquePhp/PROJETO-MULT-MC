<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reason',
        'reference_type',
        'reference_id',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    // Constants for movement types
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    // Constants for reasons
    const REASON_PURCHASE = 'purchase';
    const REASON_SALE = 'sale';
    const REASON_ADJUSTMENT = 'adjustment';
    const REASON_RETURN = 'return';
    const REASON_LOSS = 'loss';

    /**
     * Get the product that owns the movement.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who created the movement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model (polymorphic).
     */
    public function reference()
    {
        return $this->morphTo();
    }
}
