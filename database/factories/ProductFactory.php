<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'sku' => $this->faker->unique()->regexify('[A-Z0-9]{8}'),
            'barcode' => $this->faker->unique()->ean13(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'cost_price' => $this->faker->randomFloat(2, 5, 500),
            'stock' => $this->faker->numberBetween(0, 100),
            'min_stock' => $this->faker->numberBetween(5, 20),
            'category' => $this->faker->randomElement(['Materiais de Construção', 'Ferramentas', 'Elétrica', 'Hidráulica', 'Pintura']),
            'brand' => $this->faker->company(),
            'unit' => $this->faker->randomElement(['un', 'kg', 'm', 'm²', 'm³']),
            'status' => $this->faker->randomElement(['active', 'inactive'])
        ];
    }

    /**
     * Indicate that the product is active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active'
            ];
        });
    }

    /**
     * Indicate that the product has low stock.
     */
    public function lowStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock' => 5,
                'min_stock' => 10
            ];
        });
    }
}
