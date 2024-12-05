<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'product_id',
        'quantity',
        'min_quantity',
        'max_quantity',
        'location',
        'status'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'min_quantity' => 'decimal:2',
        'max_quantity' => 'decimal:2'
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_OUT_OF_STOCK = 'out_of_stock';
    const STATUS_LOW_STOCK = 'low_stock';

    // Relationships
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Stock management methods
    public function updateStock($quantity, $type, $reference = null, $notes = null)
    {
        DB::beginTransaction();
        try {
            $oldQuantity = $this->quantity;
            $this->quantity += $quantity;
            
            if ($this->quantity < 0) {
                throw new \Exception('Cannot have negative stock.');
            }

            $this->updateStatus();
            $this->save();

            // Create movement record
            $this->movements()->create([
                'type' => $type,
                'quantity' => $quantity,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $this->quantity,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'user_id' => auth()->id(),
                'notes' => $notes
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function transfer($toStore, $quantity, $notes = null)
    {
        if (!$this->hasStock($quantity)) {
            throw new \Exception('Insufficient stock for transfer.');
        }

        DB::beginTransaction();
        try {
            // Remove from current store
            $this->updateStock(-$quantity, 'transfer_out', null, "Transfer to store #{$toStore->id}: {$notes}");

            // Add to destination store
            $destinationInventory = self::firstOrCreate(
                [
                    'store_id' => $toStore->id,
                    'product_id' => $this->product_id
                ],
                [
                    'quantity' => 0,
                    'min_quantity' => $this->min_quantity,
                    'max_quantity' => $this->max_quantity,
                    'status' => self::STATUS_ACTIVE
                ]
            );

            $destinationInventory->updateStock($quantity, 'transfer_in', null, "Transfer from store #{$this->store_id}: {$notes}");

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Stock check methods
    public function hasStock($quantity)
    {
        return $this->quantity >= $quantity;
    }

    public function needsRestock()
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function exceedsMaxStock($quantity)
    {
        if (!$this->max_quantity) {
            return false;
        }
        return ($this->quantity + $quantity) > $this->max_quantity;
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->min_quantity;
    }

    // Status management
    protected function updateStatus()
    {
        if ($this->quantity <= 0) {
            $this->status = self::STATUS_OUT_OF_STOCK;
        } elseif ($this->isLowStock()) {
            $this->status = self::STATUS_LOW_STOCK;
        } else {
            $this->status = self::STATUS_ACTIVE;
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeLowStock($query)
    {
        return $query->where('status', self::STATUS_LOW_STOCK);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('status', self::STATUS_OUT_OF_STOCK);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }
}
