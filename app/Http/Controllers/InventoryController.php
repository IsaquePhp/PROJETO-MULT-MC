<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with(['store', 'product'])->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $inventories
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'location' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $inventory = Inventory::create([
            'store_id' => $request->store_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'min_quantity' => $request->min_quantity,
            'location' => $request->location,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inventory item created successfully',
            'data' => $inventory
        ], 201);
    }

    public function show($id)
    {
        $inventory = Inventory::with(['store', 'product'])->find($id);
        
        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory item not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $inventory
        ]);
    }

    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0',
            'type' => 'required|in:adjustment,increase,decrease',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $inventory = Inventory::find($id);
        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory item not found'
            ], 404);
        }

        try {
            switch ($request->type) {
                case 'adjustment':
                    $inventory->quantity = $request->quantity;
                    break;
                case 'increase':
                    $inventory->quantity += $request->quantity;
                    break;
                case 'decrease':
                    if ($inventory->quantity < $request->quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock'
                        ], 400);
                    }
                    $inventory->quantity -= $request->quantity;
                    break;
            }

            // Update status based on min_quantity
            if ($inventory->quantity <= $inventory->min_quantity) {
                $inventory->status = 'low_stock';
            } else {
                $inventory->status = 'active';
            }

            $inventory->save();

            // Create inventory movement record
            DB::table('inventory_movements')->insert([
                'inventory_id' => $inventory->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'data' => $inventory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function transferStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fromInventory = Inventory::where('store_id', $request->from_store_id)
                                    ->where('product_id', $request->product_id)
                                    ->first();

            if (!$fromInventory || $fromInventory->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock in source store'
                ], 400);
            }

            $toInventory = Inventory::firstOrCreate(
                [
                    'store_id' => $request->to_store_id,
                    'product_id' => $request->product_id
                ],
                [
                    'quantity' => 0,
                    'min_quantity' => $fromInventory->min_quantity,
                    'status' => 'active'
                ]
            );

            // Decrease stock from source store
            $fromInventory->quantity -= $request->quantity;
            if ($fromInventory->quantity <= $fromInventory->min_quantity) {
                $fromInventory->status = 'low_stock';
            }
            $fromInventory->save();

            // Increase stock in destination store
            $toInventory->quantity += $request->quantity;
            if ($toInventory->quantity > $toInventory->min_quantity) {
                $toInventory->status = 'active';
            }
            $toInventory->save();

            // Create transfer record
            DB::table('inventory_movements')->insert([
                [
                    'inventory_id' => $fromInventory->id,
                    'type' => 'transfer_out',
                    'quantity' => $request->quantity,
                    'notes' => $request->notes,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'inventory_id' => $toInventory->id,
                    'type' => 'transfer_in',
                    'quantity' => $request->quantity,
                    'notes' => $request->notes,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock transferred successfully',
                'data' => [
                    'from_inventory' => $fromInventory,
                    'to_inventory' => $toInventory
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function lowStockReport()
    {
        $lowStockItems = Inventory::with(['store', 'product'])
                                ->whereRaw('quantity <= min_quantity')
                                ->where('status', 'low_stock')
                                ->get();

        $summary = [
            'total_low_stock' => $lowStockItems->count(),
            'total_out_of_stock' => $lowStockItems->where('quantity', 0)->count()
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'low_stock_items' => $lowStockItems,
                'summary' => $summary
            ]
        ]);
    }
}
