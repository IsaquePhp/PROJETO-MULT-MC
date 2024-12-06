<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'installment_number',
        'amount',
        'due_date',
        'payment_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_LATE = 'late';

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Verifica se a parcela está atrasada
    public function isLate()
    {
        return $this->due_date < now() && $this->status === self::STATUS_PENDING;
    }

    // Registra o pagamento da parcela
    public function markAsPaid()
    {
        $this->status = self::STATUS_PAID;
        $this->payment_date = now();
        $this->save();

        // Atualiza o pagamento principal
        $this->payment->registerPayment($this->amount);
    }
}
