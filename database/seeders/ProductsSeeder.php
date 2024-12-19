<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // Garantir que temos categorias
        $category1 = Category::firstOrCreate(['name' => 'Bebidas']);
        $category2 = Category::firstOrCreate(['name' => 'Alimentos']);
        $category3 = Category::firstOrCreate(['name' => 'Limpeza']);

        // Lista de produtos
        $products = [
            [
                'name' => 'Coca-Cola 2L',
                'description' => 'Refrigerante Coca-Cola 2 litros',
                'price' => 8.99,
                'cost_price' => 5.99,
                'stock' => 100,
                'category_id' => $category1->id,
                'sku' => 'COCA2L',
                'status' => 'active',
                'unit' => 'un',
                'image' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?q=80&w=400',
            ],
            [
                'name' => 'Arroz Integral 1kg',
                'description' => 'Arroz integral tipo 1, pacote com 1kg',
                'price' => 7.50,
                'cost_price' => 4.50,
                'stock' => 150,
                'category_id' => $category2->id,
                'sku' => 'ARZ1KG',
                'status' => 'active',
                'unit' => 'kg',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=400',
            ],
            [
                'name' => 'Detergente Líquido 500ml',
                'description' => 'Detergente líquido neutro, embalagem com 500ml',
                'price' => 2.99,
                'cost_price' => 1.50,
                'stock' => 200,
                'category_id' => $category3->id,
                'sku' => 'DET500',
                'status' => 'active',
                'unit' => 'un',
                'image' => 'https://images.unsplash.com/photo-1585670140672-59c3f1e24e95?q=80&w=400',
            ],
            [
                'name' => 'Suco de Laranja 1L',
                'description' => 'Suco de laranja natural, garrafa com 1 litro',
                'price' => 12.90,
                'cost_price' => 8.90,
                'stock' => 80,
                'category_id' => $category1->id,
                'sku' => 'SLAR1L',
                'status' => 'active',
                'unit' => 'un',
                'image' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?q=80&w=400',
            ],
            [
                'name' => 'Feijão Preto 1kg',
                'description' => 'Feijão preto tipo 1, pacote com 1kg',
                'price' => 8.75,
                'cost_price' => 5.75,
                'stock' => 120,
                'category_id' => $category2->id,
                'sku' => 'FEJ1KG',
                'status' => 'active',
                'unit' => 'kg',
                'image' => 'https://images.unsplash.com/photo-1551489186-cf8726f514f8?q=80&w=400',
            ],
            [
                'name' => 'Água Sanitária 2L',
                'description' => 'Água sanitária multiuso, embalagem com 2 litros',
                'price' => 6.99,
                'cost_price' => 3.99,
                'stock' => 150,
                'category_id' => $category3->id,
                'sku' => 'AGS2L',
                'status' => 'active',
                'unit' => 'un',
                'image' => 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?q=80&w=400',
            ],
            [
                'name' => 'Cerveja Artesanal IPA 500ml',
                'description' => 'Cerveja artesanal tipo IPA, garrafa com 500ml',
                'price' => 15.90,
                'cost_price' => 9.90,
                'stock' => 60,
                'category_id' => $category1->id,
                'sku' => 'CIPA500',
                'status' => 'active',
                'unit' => 'un',
                'image' => 'https://images.unsplash.com/photo-1566633806327-68e152aaf26d?q=80&w=400',
            ],
            [
                'name' => 'Macarrão Espaguete 500g',
                'description' => 'Macarrão espaguete grano duro, pacote com 500g',
                'price' => 5.99,
                'cost_price' => 3.49,
                'stock' => 100,
                'category_id' => $category2->id,
                'sku' => 'MAC500',
                'status' => 'active',
                'unit' => 'un',
                'image' => 'https://images.unsplash.com/photo-1612966769270-e4cf4a94d322?q=80&w=400',
            ],
            [
                'name' => 'Sabão em Pó 1kg',
                'description' => 'Sabão em pó multiação, embalagem com 1kg',
                'price' => 11.50,
                'cost_price' => 7.50,
                'stock' => 90,
                'category_id' => $category3->id,
                'sku' => 'SAB1KG',
                'status' => 'active',
                'unit' => 'kg',
                'image' => 'https://images.unsplash.com/photo-1585667785517-e0b5b74248e4?q=80&w=400',
            ],
            [
                'name' => 'Energético 250ml',
                'description' => 'Bebida energética, lata com 250ml',
                'price' => 7.99,
                'cost_price' => 4.99,
                'stock' => 120,
                'category_id' => $category1->id,
                'sku' => 'ENE250',
                'status' => 'active',
                'unit' => 'un',
                'image' => 'https://images.unsplash.com/photo-1622543925917-763c34d1a86e?q=80&w=400',
            ],
        ];

        // Inserir produtos
        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
