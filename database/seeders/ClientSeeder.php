<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 50; $i++) {
            Client::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->cellphoneNumber,
                'cpf' => $faker->cpf(false),
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => $faker->stateAbbr,
                'postal_code' => $faker->postcode,
                'active' => $faker->boolean(80), // 80% chance of being active
            ]);
        }
    }
}
