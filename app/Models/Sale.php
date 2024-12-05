<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'total_amount',
        'channel',
        'country',
        'customer_id',
        'status',
        'user_id',
        'store_id',
        'customer_name',
        'customer_document',
        'payment_method',
        'payment_status',
        'sale_status',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    /**
     * The attributes that should be cast to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the user who made the sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the store where the sale was made.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the items for this sale.
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Calculate the total amount for this sale.
     */
    public function calculateTotal()
    {
        $total = $this->items->sum('total');
        $this->total_amount = $total;
        return $total;
    }

    /**
     * Add an item to the sale.
     */
    public function addItem($product, $quantity, $unit_price = null, $discount = 0)
    {
        DB::beginTransaction();
        try {
            $item = $this->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unit_price ?? $product->price,
                'discount' => $discount
            ]);

            $item->calculateTotal();
            $item->save();
            
            // Update product stock
            $item->updateProductStock();

            $this->calculateTotal();
            $this->save();

            DB::commit();
            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel the sale and restore stock.
     */
    public function cancel()
    {
        if ($this->sale_status === 'canceled') {
            throw new \Exception('Sale is already canceled.');
        }

        DB::beginTransaction();
        try {
            // Restore stock for each item
            foreach ($this->items as $item) {
                $item->restoreProductStock();
            }

            $this->sale_status = 'canceled';
            $this->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Complete the sale.
     */
    public function complete()
    {
        if ($this->sale_status !== 'pending') {
            throw new \Exception('Sale cannot be completed.');
        }

        $this->sale_status = 'completed';
        $this->payment_status = 'paid';
        $this->save();
    }
}
