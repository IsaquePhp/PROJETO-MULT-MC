<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'customer_name' => $this->faker->name(),
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'debit_card', 'pix']),
            'total_amount' => $this->faker->randomFloat(2, 50, 5000),
            'status' => Sale::STATUS_COMPLETED,
            'notes' => $this->faker->optional()->sentence()
        ];
    }

    /**
     * Indicate that the sale is cancelled.
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Sale::STATUS_CANCELLED
            ];
        });
    }
}
