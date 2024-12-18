<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['items.product', 'customer', 'store']);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Ordenação
        $query->orderBy('created_at', 'desc');

        return response()->json([
            'success' => true,
            'data' => $query->paginate($request->per_page ?? 15)
        ]);
    }

    /**
     * Store a new sale.
     */
    public function store(Request $request)
    {
        \Log::info('Recebendo requisição de venda:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:stores,id',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card,pix',
            'installments' => 'required|integer|min:1|max:12',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            \Log::error('Validação falhou:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Buscar o cliente
            $customer = Customer::findOrFail($request->customer_id);
            \Log::info('Cliente encontrado:', $customer->toArray());
            
            // Buscar a loja
            $store = Store::findOrFail($request->store_id);
            \Log::info('Loja encontrada:', $store->toArray());

            // Criar a venda
            $sale = Sale::create([
                'code' => 'V' . str_pad(Sale::count() + 1, 6, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'store_id' => $store->id,
                'customer_name' => $customer->name,
                'customer_document' => $customer->cpf_cnpj,
                'payment_method' => $request->payment_method,
                'installments' => $request->installments,
                'payment_status' => 'pending',
                'sale_status' => 'pending',
                'notes' => $request->notes,
                'user_id' => null // Usuário não é obrigatório
            ]);

            \Log::info('Venda criada:', $sale->toArray());

            $total = 0;

            // Adicionar itens à venda
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                \Log::info('Processando produto:', [
                    'product' => $product->toArray(),
                    'item' => $item
                ]);
                
                // Verificar estoque
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Produto {$product->name} não possui estoque suficiente. Disponível: {$product->stock}");
                }

                // Calcular total do item
                $itemTotal = $item['quantity'] * $item['unit_price'];

                // Validar se o total calculado bate com o total informado
                if (abs($itemTotal - $item['total']) > 0.01) {
                    throw new \Exception("Total do item {$product->name} está incorreto");
                }

                // Adicionar item
                $saleItem = $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $itemTotal
                ]);

                \Log::info('Item adicionado:', $saleItem->toArray());

                // Atualizar estoque
                $product->decrement('stock', $item['quantity']);
                \Log::info('Estoque atualizado:', [
                    'product_id' => $product->id,
                    'new_stock' => $product->stock
                ]);

                $total += $itemTotal;
            }

            // Atualizar total da venda
            $sale->update(['total' => $total]);
            \Log::info('Total da venda atualizado:', ['total' => $total]);

            // Se for pagamento em dinheiro ou débito, já marca como pago
            if (in_array($request->payment_method, ['cash', 'debit_card', 'pix'])) {
                $sale->update([
                    'payment_status' => 'paid',
                    'sale_status' => 'completed',
                    'paid_at' => now()
                ]);
                \Log::info('Venda marcada como paga');
            }

            DB::commit();
            \Log::info('Venda finalizada com sucesso');

            // Carregar os relacionamentos
            $sale->load(['items.product', 'customer', 'store']);

            return response()->json([
                'success' => true,
                'message' => 'Venda realizada com sucesso',
                'data' => $sale
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erro ao processar venda:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified sale.
     */
    public function show(Sale $sale)
    {
        return response()->json([
            'success' => true,
            'data' => $sale->load(['items.product', 'customer', 'store'])
        ]);
    }

    /**
     * Complete a sale.
     */
    public function complete(Sale $sale)
    {
        try {
            $sale->update(['sale_status' => 'completed']);
            return response()->json([
                'success' => true,
                'message' => 'Venda finalizada com sucesso',
                'data' => $sale->load(['items.product', 'customer'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar venda',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel a sale.
     */
    public function cancel(Sale $sale)
    {
        try {
            // Devolver produtos ao estoque
            foreach ($sale->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $sale->update(['sale_status' => 'cancelled']);
            
            return response()->json([
                'success' => true,
                'message' => 'Venda cancelada com sucesso',
                'data' => $sale->load(['items.product', 'customer'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar venda',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get sales report.
     */
    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'store_id' => 'nullable|exists:stores,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Sale::where('sale_status', 'completed')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date);

        if ($request->has('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $report = [
            'total_sales' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'sales_by_payment_method' => $query->groupBy('payment_method')
                ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
                ->get(),
            'sales_by_date' => $query->groupBy(DB::raw('DATE(created_at)'))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Update the status of a sale.
     */
    public function updateStatus(Request $request, Sale $sale)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,processing,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $sale->sale_status = $request->status;
            $sale->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale status updated successfully',
                'data' => $sale
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating sale status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF receipt for a sale
     */
    public function generatePdf(Sale $sale)
    {
        try {
            $sale->load(['items.product', 'customer']);
            
            $pdf = PDF::loadView('pdf.sale', compact('sale'));
            
            // Configurar o PDF sem margens
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'dpi' => 150,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true,
                'margin-top' => 0,
                'margin-right' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0
            ]);

            return $pdf->stream('pedido-' . $sale->id . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
