<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Bebidas' => [
                [
                    'name' => 'Coca-Cola 2L',
                    'description' => 'Refrigerante Coca-Cola 2 litros',
                    'sku' => 'COCA-2L',
                    'barcode' => '7891234567890',
                    'price' => 8.99,
                    'cost_price' => 6.50,
                    'stock' => 100,
                    'min_stock' => 20,
                    'brand' => 'Coca-Cola',
                    'unit' => 'Un',
                    'status' => 'active'
                ],
                [
                    'name' => 'Guaraná Antarctica 2L',
                    'description' => 'Refrigerante Guaraná Antarctica 2 litros',
                    'sku' => 'GUAR-2L',
                    'barcode' => '7891234567891',
                    'price' => 7.99,
                    'cost_price' => 5.50,
                    'stock' => 80,
                    'min_stock' => 15,
                    'brand' => 'Antarctica',
                    'unit' => 'Un',
                    'status' => 'active'
                ]
            ],
            'Mercearia' => [
                [
                    'name' => 'Arroz Branco 5kg',
                    'description' => 'Arroz branco tipo 1',
                    'sku' => 'ARR-5KG',
                    'barcode' => '7891234567892',
                    'price' => 21.90,
                    'cost_price' => 18.00,
                    'stock' => 150,
                    'min_stock' => 30,
                    'brand' => 'Tio João',
                    'unit' => 'Pct',
                    'status' => 'active'
                ],
                [
                    'name' => 'Feijão Carioca 1kg',
                    'description' => 'Feijão carioca tipo 1',
                    'sku' => 'FEI-1KG',
                    'barcode' => '7891234567893',
                    'price' => 8.90,
                    'cost_price' => 6.50,
                    'stock' => 200,
                    'min_stock' => 40,
                    'brand' => 'Camil',
                    'unit' => 'Pct',
                    'status' => 'active'
                ]
            ],
            'Hortifruti' => [
                [
                    'name' => 'Banana Prata',
                    'description' => 'Banana prata madura',
                    'sku' => 'BAN-KG',
                    'barcode' => '7891234567894',
                    'price' => 5.99,
                    'cost_price' => 3.50,
                    'stock' => 50,
                    'min_stock' => 10,
                    'brand' => 'In Natura',
                    'unit' => 'Kg',
                    'status' => 'active'
                ],
                [
                    'name' => 'Tomate',
                    'description' => 'Tomate maduro',
                    'sku' => 'TOM-KG',
                    'barcode' => '7891234567895',
                    'price' => 6.99,
                    'cost_price' => 4.50,
                    'stock' => 40,
                    'min_stock' => 8,
                    'brand' => 'In Natura',
                    'unit' => 'Kg',
                    'status' => 'active'
                ]
            ],
            'Limpeza' => [
                [
                    'name' => 'Detergente 500ml',
                    'description' => 'Detergente líquido neutro',
                    'sku' => 'DET-500',
                    'barcode' => '7891234567896',
                    'price' => 2.99,
                    'cost_price' => 1.80,
                    'stock' => 120,
                    'min_stock' => 24,
                    'brand' => 'Ypê',
                    'unit' => 'Un',
                    'status' => 'active'
                ],
                [
                    'name' => 'Sabão em Pó 1kg',
                    'description' => 'Sabão em pó multiação',
                    'sku' => 'SAB-1KG',
                    'barcode' => '7891234567897',
                    'price' => 12.90,
                    'cost_price' => 9.50,
                    'stock' => 80,
                    'min_stock' => 16,
                    'brand' => 'Omo',
                    'unit' => 'Pct',
                    'status' => 'active'
                ]
            ],
            'Higiene' => [
                [
                    'name' => 'Papel Higiênico 12un',
                    'description' => 'Papel higiênico folha dupla',
                    'sku' => 'PAP-12',
                    'barcode' => '7891234567898',
                    'price' => 18.90,
                    'cost_price' => 14.50,
                    'stock' => 60,
                    'min_stock' => 12,
                    'brand' => 'Personal',
                    'unit' => 'Pct',
                    'status' => 'active'
                ],
                [
                    'name' => 'Creme Dental 90g',
                    'description' => 'Creme dental com flúor',
                    'sku' => 'CRE-90G',
                    'barcode' => '7891234567899',
                    'price' => 4.99,
                    'cost_price' => 3.20,
                    'stock' => 100,
                    'min_stock' => 20,
                    'brand' => 'Colgate',
                    'unit' => 'Un',
                    'status' => 'active'
                ]
            ]
        ];

        foreach ($categories as $categoryName => $products) {
            $category = Category::where('name', $categoryName)->first();
            if (!$category) {
                $category = Category::create(['name' => $categoryName]);
            }

            foreach ($products as $product) {
                Product::create(array_merge($product, ['category_id' => $category->id]));
            }
        }
    }
}
