<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'sale_id',
        'company_id',
        'store_id',
        'amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'payment_method',
        'installments',
        'due_date',
        'payment_date',
        'bank_slip_code',
        'pix_code',
        'card_transaction_id',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_PARTIAL = 'partial';
    const STATUS_LATE = 'late';
    const STATUS_CANCELLED = 'cancelled';

    const METHOD_MONEY = 'money';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_DEBIT_CARD = 'debit_card';
    const METHOD_PIX = 'pix';
    const METHOD_BANK_SLIP = 'bank_slip';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    public function notifications()
    {
        return $this->hasMany(PaymentNotification::class);
    }

    // Verifica se o pagamento está atrasado
    public function isLate()
    {
        return $this->due_date < now() && $this->status !== self::STATUS_PAID;
    }

    // Atualiza o status do pagamento
    public function updateStatus()
    {
        if ($this->paid_amount >= $this->amount) {
            $this->status = self::STATUS_PAID;
        } elseif ($this->paid_amount > 0) {
            $this->status = self::STATUS_PARTIAL;
        } elseif ($this->isLate()) {
            $this->status = self::STATUS_LATE;
        }

        $this->save();
    }

    // Registra um pagamento parcial
    public function registerPayment($amount)
    {
        $this->paid_amount += $amount;
        $this->remaining_amount = $this->amount - $this->paid_amount;
        $this->payment_date = now();
        $this->updateStatus();
    }
}
