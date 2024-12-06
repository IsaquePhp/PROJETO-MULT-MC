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
        if ($request->category && $request->category !== 'Todas') {
            $query->where('category', $request->category);
        }

        // Filtro por status
        if ($request->status && $request->status !== 'Todos') {
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

        // Ordenar por ID decrescente (últimos cadastrados primeiro)
        $query->orderBy('id', 'desc');

        $products = $query->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
            'sku' => 'required|string|unique:products|regex:/^[A-Za-z0-9-]+$/',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->stock) {
                        $fail('O estoque mínimo não pode ser maior que o estoque atual.');
                    }
                },
            ],
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:10',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $product = Product::create($request->all());

            return response()->json([
                'message' => 'Produto criado com sucesso',
                'product' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
            'sku' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('products')->ignore($product->id),
                'regex:/^[A-Za-z0-9-]+$/'
            ],
            'price' => 'sometimes|required|numeric|min:0',
            'cost_price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'min_stock' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request, $product) {
                    $stock = $request->has('stock') ? $request->stock : $product->stock;
                    if ($value > $stock) {
                        $fail('O estoque mínimo não pode ser maior que o estoque atual.');
                    }
                },
            ],
            'category' => 'sometimes|required|string|max:100',
            'unit' => 'sometimes|required|string|max:10',
            'status' => 'sometimes|required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $product->update($request->all());

            return response()->json([
                'message' => 'Produto atualizado com sucesso',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar produto',
                'error' => $e->getMessage()
            ], 500);
        }
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
}
