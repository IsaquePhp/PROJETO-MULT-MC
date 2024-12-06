<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentInstallment;
use App\Models\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query();

        // Filtro por cliente
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filtro por status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filtro por método de pagamento
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtro por data de vencimento
        if ($request->due_date_start) {
            $query->where('due_date', '>=', $request->due_date_start);
        }
        if ($request->due_date_end) {
            $query->where('due_date', '<=', $request->due_date_end);
        }

        // Filtro por pagamentos atrasados
        if ($request->late === 'true') {
            $query->where('due_date', '<', now())
                  ->whereNotIn('status', [Payment::STATUS_PAID, Payment::STATUS_CANCELLED]);
        }

        // Ordenação
        $query->orderBy($request->sort_by ?? 'due_date', $request->sort_order ?? 'asc');

        $payments = $query->with(['customer', 'installments', 'notifications'])
                         ->paginate($request->per_page ?? 10);

        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'company_id' => 'required|exists:companies,id',
            'store_id' => 'required|exists:stores,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:money,credit_card,debit_card,pix,bank_slip',
            'installments' => 'required|integer|min:1',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Criar o pagamento
            $payment = Payment::create([
                'customer_id' => $request->customer_id,
                'sale_id' => $request->sale_id,
                'company_id' => $request->company_id,
                'store_id' => $request->store_id,
                'amount' => $request->amount,
                'remaining_amount' => $request->amount,
                'status' => Payment::STATUS_PENDING,
                'payment_method' => $request->payment_method,
                'installments' => $request->installments,
                'due_date' => $request->due_date,
                'notes' => $request->notes
            ]);

            // Criar parcelas se necessário
            if ($request->installments > 1) {
                $installmentAmount = round($request->amount / $request->installments, 2);
                $remainingAmount = $request->amount;
                $dueDate = Carbon::parse($request->due_date);

                for ($i = 1; $i <= $request->installments; $i++) {
                    // Para a última parcela, usar o valor restante para evitar problemas de arredondamento
                    $amount = ($i == $request->installments) ? $remainingAmount : $installmentAmount;
                    
                    PaymentInstallment::create([
                        'payment_id' => $payment->id,
                        'installment_number' => $i,
                        'amount' => $amount,
                        'due_date' => $dueDate->copy(),
                        'status' => PaymentInstallment::STATUS_PENDING
                    ]);

                    $remainingAmount -= $amount;
                    $dueDate->addMonth();
                }
            }

            // Criar notificações
            $this->createNotifications($payment);

            return response()->json([
                'message' => 'Pagamento criado com sucesso',
                'payment' => $payment->load(['installments', 'notifications'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Payment $payment)
    {
        $payment->load(['customer', 'installments', 'notifications']);
        return response()->json($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        if ($payment->status === Payment::STATUS_PAID) {
            return response()->json(['message' => 'Não é possível alterar um pagamento já quitado'], 422);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:pending,paid,partial,late,cancelled',
            'payment_method' => 'sometimes|required|in:money,credit_card,debit_card,pix,bank_slip',
            'due_date' => 'sometimes|required|date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $payment->update($request->all());
            
            if ($request->has('due_date')) {
                $this->updateNotifications($payment);
            }

            return response()->json([
                'message' => 'Pagamento atualizado com sucesso',
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Payment $payment)
    {
        if ($payment->status === Payment::STATUS_PAID) {
            return response()->json(['message' => 'Não é possível excluir um pagamento já quitado'], 422);
        }

        try {
            $payment->delete();
            return response()->json(['message' => 'Pagamento excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Registra um pagamento parcial ou total
    public function registerPayment(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:money,credit_card,debit_card,pix,bank_slip',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if ($request->amount > $payment->remaining_amount) {
                return response()->json([
                    'message' => 'O valor do pagamento não pode ser maior que o valor restante'
                ], 422);
            }

            $payment->registerPayment($request->amount);
            
            // Criar notificação de pagamento recebido
            PaymentNotification::create([
                'payment_id' => $payment->id,
                'type' => PaymentNotification::TYPE_PAYMENT_RECEIVED,
                'status' => PaymentNotification::STATUS_PENDING,
                'scheduled_for' => now(),
                'message' => "Pagamento de R$ {$request->amount} recebido em {$request->payment_date}"
            ]);

            return response()->json([
                'message' => 'Pagamento registrado com sucesso',
                'payment' => $payment->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao registrar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Lista os pagamentos atrasados
    public function latePayments(Request $request)
    {
        $query = Payment::where('due_date', '<', now())
                       ->whereNotIn('status', [Payment::STATUS_PAID, Payment::STATUS_CANCELLED]);

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $payments = $query->with(['customer', 'installments'])
                         ->orderBy('due_date', 'asc')
                         ->paginate($request->per_page ?? 10);

        return response()->json($payments);
    }

    // Cria as notificações padrão para um pagamento
    private function createNotifications(Payment $payment)
    {
        // Notificação 3 dias antes do vencimento
        PaymentNotification::create([
            'payment_id' => $payment->id,
            'type' => PaymentNotification::TYPE_DUE_DATE,
            'status' => PaymentNotification::STATUS_PENDING,
            'scheduled_for' => Carbon::parse($payment->due_date)->subDays(3),
            'message' => "Lembrete: Seu pagamento vence em 3 dias"
        ]);

        // Notificação no dia do vencimento
        PaymentNotification::create([
            'payment_id' => $payment->id,
            'type' => PaymentNotification::TYPE_DUE_DATE,
            'status' => PaymentNotification::STATUS_PENDING,
            'scheduled_for' => $payment->due_date,
            'message' => "Seu pagamento vence hoje"
        ]);
    }

    // Atualiza as notificações quando a data de vencimento é alterada
    private function updateNotifications(Payment $payment)
    {
        // Deletar notificações pendentes
        $payment->notifications()
                ->where('status', PaymentNotification::STATUS_PENDING)
                ->delete();

        // Criar novas notificações
        $this->createNotifications($payment);
    }
}
