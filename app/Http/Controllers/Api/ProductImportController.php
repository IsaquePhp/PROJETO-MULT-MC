<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MagaluService;
use Illuminate\Http\Request;

class ProductImportController extends Controller
{
    protected $magaluService;

    public function __construct(MagaluService $magaluService)
    {
        $this->magaluService = $magaluService;
    }

    /**
     * Busca produtos no Magalu
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $page = $request->get('page', 1);

        $products = $this->magaluService->searchConstructionProducts($query, $page);

        return response()->json($products);
    }

    /**
     * Importa um produto específico
     */
    public function import(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string'
        ]);

        $product = $this->magaluService->importProduct($request->product_id);

        if ($product) {
            return response()->json([
                'message' => 'Produto importado com sucesso',
                'product' => $product
            ]);
        }

        return response()->json([
            'message' => 'Erro ao importar produto'
        ], 422);
    }

    /**
     * Atualiza todos os produtos importados
     */
    public function updateAll()
    {
        try {
            $this->magaluService->updateImportedProducts();
            return response()->json([
                'message' => 'Produtos atualizados com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar produtos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
