<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['items.product', 'user', 'store']);

        // Filtros
        if ($request->has('status')) {
            $query->where('sale_status', $request->status);
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
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'customer_name' => 'required|string|max:255',
            'customer_document' => 'nullable|string|max:20',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card,pix',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'store_id' => $request->store_id,
                'customer_name' => $request->customer_name,
                'customer_document' => $request->customer_document,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'sale_status' => 'pending',
                'notes' => $request->notes
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $sale->addItem(
                    $product,
                    $item['quantity'],
                    $item['unit_price'] ?? null,
                    $item['discount'] ?? 0
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale created successfully',
                'data' => $sale->load(['items.product', 'user', 'store'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating sale',
                'error' => $e->getMessage()
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
            'data' => $sale->load(['items.product', 'user', 'store'])
        ]);
    }

    /**
     * Complete a sale.
     */
    public function complete(Sale $sale)
    {
        try {
            $sale->complete();
            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'data' => $sale->load(['items.product', 'user', 'store'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing sale',
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
            $sale->cancel();
            return response()->json([
                'success' => true,
                'message' => 'Sale cancelled successfully',
                'data' => $sale->load(['items.product', 'user', 'store'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling sale',
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
}
