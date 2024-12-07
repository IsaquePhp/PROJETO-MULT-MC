<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Faker\Factory as Faker;

class AdditionalCustomersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 20; $i++) {
            $isCompany = $faker->boolean(30); // 30% chance of being a company

            Customer::create([
                'name' => $isCompany ? $faker->company : $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->numerify('(##) #####-####'),
                'cpf_cnpj' => $isCompany ? $faker->numerify('##.###.###/####-##') : $faker->numerify('###.###.###-##'),
                'type' => $isCompany ? 'company' : 'individual',
                'status' => $faker->randomElement(['active', 'inactive']),
                'notes' => $faker->optional()->sentence,
                'address' => $faker->streetAddress,
                'address_number' => $faker->buildingNumber,
                'complement' => $faker->optional()->secondaryAddress,
                'neighborhood' => $faker->word,
                'city' => $faker->city,
                'state' => $faker->stateAbbr,
                'zip_code' => $faker->numerify('#####-###'),
                'company_name' => $isCompany ? $faker->company : null,
                'trading_name' => $isCompany ? $faker->company : null,
                'state_registration' => $isCompany ? $faker->numerify('########-#') : null,
                'credit_limit' => $faker->randomFloat(2, 1000, 50000),
                'total_debt' => $faker->randomFloat(2, 0, 5000),
                'available_credit' => $faker->randomFloat(2, 0, 45000),
                'company_id' => 1 // Assumindo que existe pelo menos uma empresa com ID 1
            ]);
        }
    }
}
