<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Materiais Básicos',
                'description' => 'Materiais básicos para construção',
                'active' => true
            ],
            [
                'name' => 'Ferramentas',
                'description' => 'Ferramentas para construção',
                'active' => true
            ],
            [
                'name' => 'Hidráulica',
                'description' => 'Materiais para instalações hidráulicas',
                'active' => true
            ],
            [
                'name' => 'Elétrica',
                'description' => 'Materiais para instalações elétricas',
                'active' => true
            ],
            [
                'name' => 'Pintura',
                'description' => 'Materiais para pintura',
                'active' => true
            ],
            [
                'name' => 'Acabamento',
                'description' => 'Materiais para acabamento',
                'active' => true
            ],
            [
                'name' => 'Ferragens',
                'description' => 'Ferragens em geral',
                'active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
