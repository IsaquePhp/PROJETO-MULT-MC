<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'barcode',
        'price',
        'cost_price',
        'stock',
        'min_stock',
        'category',
        'brand',
        'unit',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer'
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Get the sale items for the product.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Update stock quantity
     */
    public function updateStock(int $quantity, string $type = 'add')
    {
        if ($type === 'add') {
            $this->stock += $quantity;
        } else {
            $this->stock -= $quantity;
        }

        $this->save();
    }

    /**
     * Check if product needs restock
     */
    public function needsRestock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Get total sales value
     */
    public function getTotalSalesValue()
    {
        return $this->saleItems()
            ->whereHas('sale', function ($query) {
                $query->where('status', Sale::STATUS_COMPLETED);
            })
            ->sum('total_price');
    }

    /**
     * Get total sales quantity
     */
    public function getTotalSalesQuantity()
    {
        return $this->saleItems()
            ->whereHas('sale', function ($query) {
                $query->where('status', Sale::STATUS_COMPLETED);
            })
            ->sum('quantity');
    }
}
