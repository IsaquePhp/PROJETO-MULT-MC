<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\Product;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    protected $with = ['product'];

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
     * Calculate the subtotal for this item.
     */
    public function calculateSubtotal()
    {
        $this->subtotal = $this->quantity * $this->price;
        $this->save();
        return $this->subtotal;
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
