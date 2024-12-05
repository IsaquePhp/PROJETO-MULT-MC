<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashFlow extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cash_flow';

    protected $fillable = [
        'store_id',
        'user_id',
        'type',
        'category',
        'amount',
        'payment_method',
        'reference_type',
        'reference_id',
        'description',
        'date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date'
    ];

    // Constants for types
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    // Constants for categories
    const CATEGORY_SALE = 'sale';
    const CATEGORY_PURCHASE = 'purchase';
    const CATEGORY_SALARY = 'salary';
    const CATEGORY_RENT = 'rent';
    const CATEGORY_UTILITIES = 'utilities';
    const CATEGORY_OTHER = 'other';

    // Relationships
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('type', self::TYPE_INCOME);
    }

    public function scopeExpense($query)
    {
        return $query->where('type', self::TYPE_EXPENSE);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Get balance for a date range
    public static function getBalance($storeId, $startDate, $endDate)
    {
        $income = self::where('store_id', $storeId)
            ->income()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $expense = self::where('store_id', $storeId)
            ->expense()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        return $income - $expense;
    }
}
