<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Log dos parâmetros recebidos
            Log::info('Parâmetros de filtro:', $request->all());

            // Filtro por busca (nome ou SKU)
            if ($request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            // Carregar relacionamentos
            $query->with('category');

            // Ordenação
            $query->orderBy('created_at', 'desc');

            // Paginação com todos os resultados
            $products = $query->get();

            // Log dos resultados
            Log::info('Total de produtos encontrados: ' . $products->count());

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar produtos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar produtos',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
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

            $product->fill($request->all());
            $product->save();

            return response()->json($product->fresh());
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao atualizar produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Verificar se o produto existe
            if (!$product) {
                return response()->json([
                    'message' => 'Produto não encontrado'
                ], 404);
            }

            // Verificar se o produto pode ser excluído (adicione suas regras de negócio aqui)
            // Por exemplo, verificar se não está em pedidos pendentes, etc.

            // Log antes da exclusão
            Log::info('Tentando excluir produto: ' . $product->id);

            // Excluir o produto
            $product->delete();

            // Log após a exclusão bem-sucedida
            Log::info('Produto excluído com sucesso: ' . $product->id);

            return response()->json([
                'message' => 'Produto excluído com sucesso'
            ]);
        } catch (\Exception $e) {
            // Log do erro
            Log::error('Erro ao excluir produto: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

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

    public function search(Request $request)
    {
        try {
            $query = $request->get('q');
            
            if (!$query) {
                return response()->json([]);
            }

            $products = Product::where('name', 'like', "%{$query}%")
                ->orWhere('sku', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with('category')
                ->limit(10)
                ->get();

            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar produtos: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao buscar produtos',
                'error' => $e->getMessage()
            ], 500);
        }
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
