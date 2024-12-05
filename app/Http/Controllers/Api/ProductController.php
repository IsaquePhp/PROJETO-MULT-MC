<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->brand, function ($query, $brand) {
                return $query->where('brand', $brand);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->low_stock, function ($query) {
                return $query->whereColumn('stock', '<=', 'min_stock');
            })
            ->orderBy($request->sort_by ?? 'name', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 10);

        return response()->json($products);
    }

    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products',
            'barcode' => 'nullable|string|unique:products',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category' => 'required|string',
            'brand' => 'nullable|string',
            'unit' => 'required|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::create($request->all());

            // Register initial stock movement if stock > 0
            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_IN,
                    'quantity' => $product->stock,
                    'reason' => StockMovement::REASON_ADJUSTMENT,
                    'notes' => 'Initial stock',
                    'user_id' => auth()->id()
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Produto criado com sucesso',
                'product' => $product
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar produto',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load('stockMovements.user');
        
        // Add sales statistics
        $product->total_sales_value = $product->getTotalSalesValue();
        $product->total_sales_quantity = $product->getTotalSalesQuantity();

        return response()->json($product);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'cost_price' => 'sometimes|required|numeric|min:0',
            'min_stock' => 'sometimes|required|integer|min:0',
            'category' => 'sometimes|required|string',
            'brand' => 'nullable|string',
            'unit' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:active,inactive'
        ]);

        $product->update($request->all());

        return response()->json([
            'message' => 'Produto atualizado com sucesso',
            'product' => $product
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        if ($product->stock > 0) {
            return response()->json([
                'message' => 'Não é possível excluir produto com estoque'
            ], 400);
        }

        $product->delete();

        return response()->json([
            'message' => 'Produto excluído com sucesso'
        ]);
    }

    /**
     * Update product stock.
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:add,remove',
            'reason' => 'required|in:purchase,adjustment,return,loss',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Check if we have enough stock for removal
            if ($request->type === 'remove' && $product->stock < $request->quantity) {
                throw new \Exception('Estoque insuficiente');
            }

            // Update product stock
            $product->updateStock(
                $request->quantity,
                $request->type
            );

            // Create stock movement record
            StockMovement::create([
                'product_id' => $product->id,
                'type' => $request->type === 'add' ? StockMovement::TYPE_IN : StockMovement::TYPE_OUT,
                'quantity' => $request->quantity,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Estoque atualizado com sucesso',
                'product' => $product->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao atualizar estoque',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get stock movement history.
     */
    public function stockHistory(Request $request, Product $product)
    {
        $movements = $product->stockMovements()
            ->with('user:id,name')
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->reason, function ($query, $reason) {
                return $query->where('reason', $reason);
            })
            ->when($request->date_from, function ($query, $date) {
                return $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                return $query->whereDate('created_at', '<=', $date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response()->json($movements);
    }

    /**
     * Get list of product categories.
     */
    public function categories()
    {
        $categories = Product::distinct('category')
            ->pluck('category')
            ->filter()
            ->values();

        return response()->json($categories);
    }

    /**
     * Get list of product brands.
     */
    public function brands()
    {
        $brands = Product::distinct('brand')
            ->pluck('brand')
            ->filter()
            ->values();

        return response()->json($brands);
    }

    /**
     * Get low stock products report.
     */
    public function lowStockReport()
    {
        $products = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('status', Product::STATUS_ACTIVE)
            ->get();

        return response()->json([
            'total_products' => $products->count(),
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $product->stock,
                    'min_stock' => $product->min_stock,
                    'needed_quantity' => $product->min_stock - $product->stock
                ];
            })
        ]);
    }
}
