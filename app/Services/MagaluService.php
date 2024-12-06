<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

class MagaluService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.magalu.base_url', 'https://dummyjson.com');
        $this->apiKey = config('services.magalu.api_key');
    }

    /**
     * Busca produtos de construção civil
     * Usando temporariamente a API pública dummyjson.com para testes
     */
    public function searchConstructionProducts($query = '', $page = 1)
    {
        try {
            $response = Http::get($this->baseUrl . '/products/search', [
                'q' => $query,
                'limit' => 20,
                'skip' => ($page - 1) * 20
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Transformar os dados para o formato esperado
                $products = array_map(function($item) {
                    return [
                        'id' => $item['id'],
                        'name' => $item['title'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'image_url' => $item['thumbnail'],
                        'category' => $item['category'],
                        'brand' => $item['brand']
                    ];
                }, $data['products']);

                return [
                    'products' => $products,
                    'total' => $data['total'],
                    'page' => $page
                ];
            }

            return [
                'products' => [],
                'total' => 0,
                'page' => $page
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar produtos: ' . $e->getMessage());
            return [
                'products' => [],
                'total' => 0,
                'page' => $page
            ];
        }
    }

    /**
     * Importa um produto para o sistema
     */
    public function importProduct($productId)
    {
        try {
            $response = Http::get($this->baseUrl . '/products/' . $productId);

            if ($response->successful()) {
                $productData = $response->json();

                // Gerar um SKU único
                $sku = 'MAG-' . strtoupper(substr(md5($productData['id']), 0, 8));

                return Product::create([
                    'name' => $productData['title'],
                    'description' => $productData['description'],
                    'sku' => $sku,
                    'price' => $productData['price'],
                    'cost_price' => $productData['price'] * 0.7, // 70% do preço como custo
                    'stock' => 0,
                    'min_stock' => 5,
                    'category' => $productData['category'],
                    'brand' => $productData['brand'],
                    'unit' => 'un',
                    'status' => 'active',
                    'magalu_id' => $productData['id']
                ]);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Erro ao importar produto: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualiza preços e informações de produtos já importados
     */
    public function updateImportedProducts()
    {
        try {
            $products = Product::whereNotNull('magalu_id')->get();

            foreach ($products as $product) {
                $response = Http::get($this->baseUrl . '/products/' . $product->magalu_id);

                if ($response->successful()) {
                    $productData = $response->json();
                    
                    $product->update([
                        'price' => $productData['price'],
                        'description' => $productData['description'],
                        'brand' => $productData['brand']
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar produtos: ' . $e->getMessage());
            throw $e;
        }
    }
}
