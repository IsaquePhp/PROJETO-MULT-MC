<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filtro por busca
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }

        // Filtro por categoria
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filtro por estoque
        if ($request->stock) {
            switch ($request->stock) {
                case 'low':
                    $query->whereColumn('stock', '<=', 'min_stock');
                    break;
                case 'out':
                    $query->where('stock', 0);
                    break;
                case 'in':
                    $query->where('stock', '>', 0);
                    break;
            }
        }

        $products = $query->with('category')->orderBy('id', 'desc')->get();
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => ['required', 'string', Rule::unique('products')->ignore($product->id)],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return response()->json(['message' => 'Produto excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkSku($sku)
    {
        $exists = Product::where('sku', $sku)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function categories()
    {
        $categories = Product::distinct()->pluck('category');
        return response()->json($categories);
    }

    public function updateStock(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer',
            'type' => 'required|in:add,subtract',
            'reason' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $quantity = $request->type === 'add' ? $request->quantity : -$request->quantity;
            
            if ($product->stock + $quantity < 0) {
                return response()->json(['message' => 'Estoque insuficiente'], 422);
            }

            $product->stock += $quantity;
            $product->save();

            return response()->json([
                'message' => 'Estoque atualizado com sucesso',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar estoque',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Product $product)
    {
        $product->status = $product->status === Product::STATUS_ACTIVE 
            ? Product::STATUS_INACTIVE 
            : Product::STATUS_ACTIVE;
        
        $product->save();

        return response()->json([
            'message' => $product->status === Product::STATUS_ACTIVE 
                ? 'Produto ativado com sucesso.' 
                : 'Produto inativado com sucesso.',
            'status' => $product->status
        ]);
    }
}
