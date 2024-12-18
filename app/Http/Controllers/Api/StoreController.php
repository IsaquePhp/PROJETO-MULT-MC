<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Lista todas as lojas do usuário autenticado
     */
    public function index()
    {
        $stores = Store::where('company_id', Auth::user()->company_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stores
        ]);
    }

    /**
     * Retorna os detalhes de uma loja específica
     */
    public function show($id)
    {
        $store = Store::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }

    /**
     * Cria uma nova loja
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'is_matrix' => 'boolean'
        ]);

        // Se for matriz, verifica se já existe outra matriz
        if ($validated['is_matrix'] ?? false) {
            $existingMatrix = Store::where('company_id', Auth::user()->company_id)
                ->where('is_matrix', true)
                ->exists();

            if ($existingMatrix) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe uma loja matriz cadastrada'
                ], 422);
            }
        }

        $store = Store::create(array_merge($validated, [
            'company_id' => Auth::user()->company_id
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Loja criada com sucesso',
            'data' => $store
        ], 201);
    }

    /**
     * Atualiza uma loja existente
     */
    public function update(Request $request, $id)
    {
        $store = Store::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'is_matrix' => 'boolean'
        ]);

        $store->update($validated);

        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }

    /**
     * Remove uma loja
     */
    public function destroy($id)
    {
        $store = Store::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        $store->delete();

        return response()->noContent();
    }

    /**
     * Retorna a loja padrão (matriz) do usuário
     */
    public function getDefault()
    {
        $store = Store::where('company_id', Auth::user()->company_id)
            ->where('is_matrix', true)
            ->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Loja não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }
}
