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
        $stores = Store::where('user_id', Auth::id())
            ->select('id', 'name', 'type', 'description')
            ->get();

        return response()->json($stores);
    }

    /**
     * Retorna os detalhes de uma loja específica
     */
    public function show($id)
    {
        $store = Store::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($store);
    }

    /**
     * Cria uma nova loja
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        $store = Store::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json($store, 201);
    }

    /**
     * Atualiza uma loja existente
     */
    public function update(Request $request, $id)
    {
        $store = Store::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        $store->update($validated);

        return response()->json($store);
    }

    /**
     * Remove uma loja
     */
    public function destroy($id)
    {
        $store = Store::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $store->delete();

        return response()->noContent();
    }
}
