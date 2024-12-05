<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $query = Inventory::with(['product', 'store']);

        // Filtros
        if ($request->has('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('low_stock')) {
            $query->lowStock();
        }

        if ($request->has('out_of_stock')) {
            $query->outOfStock();
        }

        // Ordenação
        $query->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return response()->json([
            'success' => true,
            'data' => $query->paginate($request->per_page ?? 15)
        ]);
    }

    /**
     * Store a new inventory item.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'max_quantity' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar se já existe um registro para este produto nesta loja
        $existing = Inventory::where('store_id', $request->store_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This product already exists in this store\'s inventory'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $inventory = Inventory::create([
                'store_id' => $request->store_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'min_quantity' => $request->min_quantity,
                'max_quantity' => $request->max_quantity,
                'location' => $request->location,
                'status' => $request->quantity > 0 ? Inventory::STATUS_ACTIVE : Inventory::STATUS_OUT_OF_STOCK
            ]);

            // Registrar movimento inicial
            if ($request->quantity > 0) {
                $inventory->updateStock($request->quantity, 'initial', null, 'Initial stock');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inventory item created successfully',
                'data' => $inventory->load(['product', 'store'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating inventory item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified inventory item.
     */
    public function show(Inventory $inventory)
    {
        return response()->json([
            'success' => true,
            'data' => $inventory->load(['product', 'store', 'movements'])
        ]);
    }

    /**
     * Update inventory stock.
     */
    public function updateStock(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'type' => 'required|string|in:adjustment,purchase,return,loss,damage',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $inventory->updateStock($request->quantity, $request->type, null, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'data' => $inventory->load(['product', 'store'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating stock',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Transfer stock between stores.
     */
    public function transfer(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'to_store_id' => 'required|exists:stores,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $toStore = Store::findOrFail($request->to_store_id);
            $inventory->transfer($toStore, $request->quantity, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Stock transferred successfully',
                'data' => $inventory->load(['product', 'store'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error transferring stock',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get low stock report.
     */
    public function lowStockReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'nullable|exists:stores,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Inventory::with(['product', 'store'])
            ->where(function($q) {
                $q->lowStock()
                  ->orWhere->outOfStock();
            });

        if ($request->has('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $report = [
            'low_stock_items' => $query->get()->map(function($item) {
                return [
                    'store' => $item->store->name,
                    'product' => $item->product->name,
                    'current_quantity' => $item->quantity,
                    'min_quantity' => $item->min_quantity,
                    'status' => $item->status,
                    'last_movement' => $item->movements()->latest()->first()
                ];
            }),
            'summary' => [
                'total_low_stock' => $query->lowStock()->count(),
                'total_out_of_stock' => $query->outOfStock()->count()
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
}
