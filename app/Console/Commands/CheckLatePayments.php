<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\PaymentNotification;
use Carbon\Carbon;

class CheckLatePayments extends Command
{
    protected $signature = 'payments:check-late';
    protected $description = 'Verifica pagamentos atrasados e gera notificações';

    public function handle()
    {
        $this->info('Verificando pagamentos atrasados...');

        // Buscar pagamentos que venceram hoje
        $todayPayments = Payment::where('due_date', Carbon::today())
            ->whereNotIn('status', [Payment::STATUS_PAID, Payment::STATUS_CANCELLED])
            ->get();

        foreach ($todayPayments as $payment) {
            PaymentNotification::create([
                'payment_id' => $payment->id,
                'type' => PaymentNotification::TYPE_LATE_PAYMENT,
                'status' => PaymentNotification::STATUS_PENDING,
                'scheduled_for' => now(),
                'message' => "O pagamento de R$ {$payment->amount} venceu hoje"
            ]);

            $payment->status = Payment::STATUS_LATE;
            $payment->save();
        }

        // Buscar pagamentos já atrasados que ainda não têm notificação de atraso hoje
        $latePayments = Payment::where('due_date', '<', Carbon::today())
            ->where('status', Payment::STATUS_LATE)
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('type', PaymentNotification::TYPE_LATE_PAYMENT)
                      ->whereDate('created_at', Carbon::today());
            })
            ->get();

        foreach ($latePayments as $payment) {
            $daysLate = Carbon::parse($payment->due_date)->diffInDays(now());
            
            // Criar notificação apenas para 3, 5, 10, 15, 30 dias de atraso
            if (in_array($daysLate, [3, 5, 10, 15, 30])) {
                PaymentNotification::create([
                    'payment_id' => $payment->id,
                    'type' => PaymentNotification::TYPE_LATE_PAYMENT,
                    'status' => PaymentNotification::STATUS_PENDING,
                    'scheduled_for' => now(),
                    'message' => "O pagamento de R$ {$payment->amount} está atrasado há {$daysLate} dias"
                ]);
            }
        }

        $this->info('Verificação concluída!');
    }
}
