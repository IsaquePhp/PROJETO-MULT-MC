<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'type',
        'status',
        'scheduled_for',
        'sent_at',
        'message'
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
    ];

    const TYPE_DUE_DATE = 'due_date';
    const TYPE_LATE_PAYMENT = 'late_payment';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Marca a notificação como enviada
    public function markAsSent()
    {
        $this->status = self::STATUS_SENT;
        $this->sent_at = now();
        $this->save();
    }

    // Marca a notificação como falha
    public function markAsFailed()
    {
        $this->status = self::STATUS_FAILED;
        $this->save();
    }
}
