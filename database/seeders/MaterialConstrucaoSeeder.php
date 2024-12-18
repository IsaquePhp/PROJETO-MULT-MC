<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class MaterialConstrucaoSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Materiais Básicos' => [
                [
                    'name' => 'Cimento CP II 50kg',
                    'description' => 'Cimento Portland CP II de alta qualidade, saco com 50kg',
                    'sku' => 'CIM-50KG',
                    'barcode' => '7891234567800',
                    'price' => 39.90,
                    'cost_price' => 32.00,
                    'stock' => 100,
                    'min_stock' => 20,
                    'brand' => 'Votorantim',
                    'unit' => 'Saco',
                    'status' => 'active'
                ],
                [
                    'name' => 'Areia Média 20kg',
                    'description' => 'Areia média lavada para construção',
                    'sku' => 'ARE-20KG',
                    'barcode' => '7891234567801',
                    'price' => 12.90,
                    'cost_price' => 8.00,
                    'stock' => 200,
                    'min_stock' => 50,
                    'brand' => 'Mineração XYZ',
                    'unit' => 'Saco',
                    'status' => 'active'
                ]
            ],
            'Ferramentas' => [
                [
                    'name' => 'Martelo Profissional',
                    'description' => 'Martelo com cabo em fibra de vidro, cabeça forjada',
                    'sku' => 'MAR-001',
                    'barcode' => '7891234567802',
                    'price' => 45.90,
                    'cost_price' => 28.00,
                    'stock' => 50,
                    'min_stock' => 10,
                    'brand' => 'Tramontina',
                    'unit' => 'Un',
                    'status' => 'active'
                ],
                [
                    'name' => 'Trena 5m',
                    'description' => 'Trena profissional com trava',
                    'sku' => 'TRE-5M',
                    'barcode' => '7891234567803',
                    'price' => 19.90,
                    'cost_price' => 12.00,
                    'stock' => 80,
                    'min_stock' => 20,
                    'brand' => 'Stanley',
                    'unit' => 'Un',
                    'status' => 'active'
                ]
            ],
            'Elétrica' => [
                [
                    'name' => 'Fio 2.5mm 100m',
                    'description' => 'Fio elétrico flexível 2.5mm, rolo com 100m',
                    'sku' => 'FIO-2.5',
                    'barcode' => '7891234567804',
                    'price' => 189.90,
                    'cost_price' => 145.00,
                    'stock' => 30,
                    'min_stock' => 5,
                    'brand' => 'Sil',
                    'unit' => 'Rolo',
                    'status' => 'active'
                ]
            ],
            'Hidráulica' => [
                [
                    'name' => 'Tubo PVC 100mm 6m',
                    'description' => 'Tubo PVC para esgoto 100mm, barra com 6m',
                    'sku' => 'PVC-100',
                    'barcode' => '7891234567805',
                    'price' => 89.90,
                    'cost_price' => 65.00,
                    'stock' => 40,
                    'min_stock' => 8,
                    'brand' => 'Tigre',
                    'unit' => 'Un',
                    'status' => 'active'
                ]
            ],
            'Pintura' => [
                [
                    'name' => 'Tinta Acrílica 18L',
                    'description' => 'Tinta acrílica premium, lata com 18 litros',
                    'sku' => 'TIN-18L',
                    'barcode' => '7891234567806',
                    'price' => 289.90,
                    'cost_price' => 210.00,
                    'stock' => 40,
                    'min_stock' => 8,
                    'brand' => 'Suvinil',
                    'unit' => 'Lata',
                    'status' => 'active'
                ]
            ],
            'Acabamento' => [
                [
                    'name' => 'Porcelanato 60x60',
                    'description' => 'Porcelanato acetinado 60x60cm',
                    'sku' => 'POR-60X60',
                    'barcode' => '7891234567807',
                    'price' => 89.90,
                    'cost_price' => 65.00,
                    'stock' => 200,
                    'min_stock' => 40,
                    'brand' => 'Portinari',
                    'unit' => 'm²',
                    'status' => 'active'
                ]
            ],
            'Ferragens' => [
                [
                    'name' => 'Fechadura Inox',
                    'description' => 'Fechadura em aço inox para porta interna',
                    'sku' => 'FEC-INOX',
                    'barcode' => '7891234567808',
                    'price' => 79.90,
                    'cost_price' => 55.00,
                    'stock' => 35,
                    'min_stock' => 7,
                    'brand' => 'Papaiz',
                    'unit' => 'Unidade',
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
