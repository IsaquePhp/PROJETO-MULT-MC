<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Camiseta Básica',
                'description' => 'Camiseta 100% algodão, disponível em várias cores',
                'sku' => 'CAM-001',
                'price' => 49.90,
                'cost_price' => 25.00,
                'stock' => 100,
                'min_stock' => 20,
                'category' => 'Roupas',
                'unit' => 'un',
                'status' => 'active'
            ],
            [
                'name' => 'Tênis Esportivo',
                'description' => 'Tênis para corrida e caminhada',
                'sku' => 'TEN-001',
                'price' => 199.90,
                'cost_price' => 100.00,
                'stock' => 50,
                'min_stock' => 10,
                'category' => 'Calçados',
                'unit' => 'par',
                'status' => 'active'
            ],
            [
                'name' => 'Relógio Digital',
                'description' => 'Relógio resistente à água com múltiplas funções',
                'sku' => 'REL-001',
                'price' => 150.00,
                'cost_price' => 75.00,
                'stock' => 30,
                'min_stock' => 5,
                'category' => 'Acessórios',
                'unit' => 'un',
                'status' => 'active'
            ],
            [
                'name' => 'Notebook Basic',
                'description' => 'Notebook ideal para tarefas básicas e navegação',
                'sku' => 'NOT-001',
                'price' => 2499.90,
                'cost_price' => 1800.00,
                'stock' => 15,
                'min_stock' => 3,
                'category' => 'Eletrônicos',
                'unit' => 'un',
                'status' => 'active'
            ],
            [
                'name' => 'Sofá 3 Lugares',
                'description' => 'Sofá confortável em tecido suede',
                'sku' => 'SOF-001',
                'price' => 1299.90,
                'cost_price' => 800.00,
                'stock' => 8,
                'min_stock' => 2,
                'category' => 'Móveis',
                'unit' => 'un',
                'status' => 'active'
            ],
            [
                'name' => 'Vaso Decorativo',
                'description' => 'Vaso em cerâmica para plantas ou decoração',
                'sku' => 'VAS-001',
                'price' => 89.90,
                'cost_price' => 45.00,
                'stock' => 40,
                'min_stock' => 8,
                'category' => 'Decoração',
                'unit' => 'un',
                'status' => 'active'
            ],
            [
                'name' => 'Boneco de Ação',
                'description' => 'Boneco articulado de super-herói',
                'sku' => 'BON-001',
                'price' => 129.90,
                'cost_price' => 65.00,
                'stock' => 25,
                'min_stock' => 5,
                'category' => 'Brinquedos',
                'unit' => 'un',
                'status' => 'active'
            ],
            [
                'name' => 'Livro Best-seller',
                'description' => 'Romance best-seller internacional',
                'sku' => 'LIV-001',
                'price' => 59.90,
                'cost_price' => 30.00,
                'stock' => 60,
                'min_stock' => 12,
                'category' => 'Livros',
                'unit' => 'un',
                'status' => 'active'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
