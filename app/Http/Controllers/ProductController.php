<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Aplicar filtros se existirem
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Ordenação
        $query->orderBy('created_at', 'desc');

        // Paginação
        $products = $query->paginate($request->get('per_page', 10));

        // Converter URLs relativas em absolutas
        $products->through(function ($product) {
            if ($product->image) {
                $product->image = asset($product->image);
            }
            return $product;
        });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string',
            'status' => 'required|in:active,inactive',
            'sku' => 'required|string|unique:products',
            'image' => 'nullable|image|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Log para debug
            \Log::info('Request data:', $request->all());
            \Log::info('Has file?', ['hasFile' => $request->hasFile('image')]);
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                \Log::info('Image details:', [
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize()
                ]);

                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/products', $filename, 'public');
                \Log::info('Image stored at:', ['path' => $path]);
                
                $validatedData['image'] = '/storage/' . $path;
            }

            $product = Product::create($validatedData);
            DB::commit();

            // Retornar produto com URL completa da imagem
            if ($product->image) {
                $product->image = asset($product->image);
            }

            return response()->json($product, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao criar produto: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Erro ao criar produto: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            $product->image = asset($product->image);
        }
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string',
            'status' => 'required|in:active,inactive',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'image' => 'nullable|image|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Log para debug
            \Log::info('Update request data:', $request->all());
            \Log::info('Has file?', ['hasFile' => $request->hasFile('image')]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                \Log::info('Update image details:', [
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize()
                ]);

                // Remover imagem antiga se existir
                if ($product->image) {
                    $oldPath = str_replace('/storage/', '', parse_url($product->image, PHP_URL_PATH));
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                        \Log::info('Old image deleted:', ['path' => $oldPath]);
                    }
                }

                // Upload da nova imagem
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/products', $filename, 'public');
                \Log::info('New image stored at:', ['path' => $path]);
                
                $validatedData['image'] = '/storage/' . $path;
            }

            $product->update($validatedData);
            DB::commit();

            // Retornar produto com URL completa da imagem
            $product = $product->fresh();
            if ($product->image) {
                $product->image = asset($product->image);
            }

            return response()->json($product);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao atualizar produto: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Erro ao atualizar produto: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function updateStock(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer',
            'type' => 'required|in:add,subtract',
            'reason' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quantity = $request->type === 'add' ? $request->quantity : -$request->quantity;
        
        if ($product->stock + $quantity < 0) {
            return response()->json(['message' => 'Insufficient stock'], 422);
        }

        $product->stock += $quantity;
        $product->save();

        // Registrar movimentação de estoque
        $product->stockMovements()->create([
            'quantity' => $quantity,
            'type' => $request->type,
            'reason' => $request->reason,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Stock updated successfully',
            'current_stock' => $product->stock
        ]);
    }

    public function stockHistory(Product $product)
    {
        $history = $product->stockMovements()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }

    /**
     * Remove a imagem inválida do produto
     */
    public function removeImage(Product $product)
    {
        try {
            if ($product->image) {
                // Tentar remover o arquivo físico se existir
                $path = str_replace(asset('storage/'), '', $product->image);
                Storage::disk('public')->delete($path);
                
                // Atualizar o produto removendo a referência à imagem
                $product->update(['image' => null]);
            }
            
            return response()->json(['message' => 'Imagem removida com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao remover imagem'], 500);
        }
    }
}
