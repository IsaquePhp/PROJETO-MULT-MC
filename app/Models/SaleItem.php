<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Sale;
use App\Models\Product;

class SaleItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
        'total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    /**
     * Get the sale that owns the item.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product of this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the total for this item.
     */
    public function calculateTotal()
    {
        $subtotal = $this->quantity * $this->unit_price;
        $this->total = $subtotal - ($subtotal * ($this->discount / 100));
        return $this->total;
    }

    /**
     * Update product stock after sale.
     */
    public function updateProductStock()
    {
        $product = $this->product;
        if ($product) {
            $product->updateStock(-$this->quantity, 'sale', $this->sale_id);
        }
    }

    /**
     * Restore product stock when canceling sale.
     */
    public function restoreProductStock()
    {
        $product = $this->product;
        if ($product) {
            $product->updateStock($this->quantity, 'sale_canceled', $this->sale_id);
        }
    }
}
