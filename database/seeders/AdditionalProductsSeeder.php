<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Faker\Factory as Faker;

class AdditionalProductsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');
        
        // Pegar todas as categorias existentes
        $categories = Category::pluck('name')->toArray();
        
        // Lista de produtos por categoria para manter coerência
        $productsByCategory = [
            'Eletrônicos' => [
                'Smartphone Samsung Galaxy',
                'iPhone Pro Max',
                'Notebook Dell',
                'Smart TV LG',
                'Fone de Ouvido JBL'
            ],
            'Roupas' => [
                'Camiseta Básica',
                'Calça Jeans',
                'Vestido Floral',
                'Blazer',
                'Jaqueta de Couro'
            ],
            'Alimentos' => [
                'Arroz Integral',
                'Feijão Preto',
                'Macarrão Integral',
                'Azeite Extra Virgem',
                'Café em Grãos'
            ],
            'Móveis' => [
                'Sofá 3 Lugares',
                'Mesa de Jantar',
                'Guarda-Roupa',
                'Cama Box Queen',
                'Poltrona Reclinável'
            ]
        ];

        for ($i = 0; $i < 20; $i++) {
            // Escolher uma categoria aleatória
            $category = $faker->randomElement($categories);
            
            // Escolher um nome de produto apropriado para a categoria
            $productName = $faker->randomElement($productsByCategory[$category] ?? ['Produto Genérico']);
            
            // Gerar SKU único
            $sku = strtoupper($faker->bothify('??###??'));
            
            Product::create([
                'name' => $productName . ' ' . $faker->randomNumber(3),
                'description' => $faker->paragraph(2),
                'price' => $faker->randomFloat(2, 10, 5000),
                'cost_price' => $faker->randomFloat(2, 5, 2500),
                'stock' => $faker->numberBetween(0, 100),
                'min_stock' => $faker->numberBetween(5, 20),
                'sku' => $sku,
                'barcode' => $faker->ean13(),
                'status' => $faker->randomElement(['active', 'inactive']),
                'category' => $category,
                'brand' => $faker->company,
                'weight' => $faker->randomFloat(2, 0.1, 30),
                'width' => $faker->randomFloat(2, 5, 200),
                'height' => $faker->randomFloat(2, 5, 200),
                'length' => $faker->randomFloat(2, 5, 200),
                'unit' => $faker->randomElement(['un', 'kg', 'l', 'm', 'cx'])
            ]);
        }
    }
}
