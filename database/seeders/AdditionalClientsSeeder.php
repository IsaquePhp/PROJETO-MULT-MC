<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use Faker\Factory as Faker;

class AdditionalClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('pt_BR');

        $clients = [
            [
                'name' => 'João Silva',
                'email' => 'joao.silva@email.com',
                'phone' => '(11) 98765-4321',
                'cpf' => '123.456.789-00',
                'address' => 'Rua das Flores, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'postal_code' => '01234-567',
                'active' => true,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@email.com',
                'phone' => '(11) 97654-3210',
                'cpf' => '987.654.321-00',
                'address' => 'Avenida Paulista, 1000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'postal_code' => '04567-890',
                'active' => true,
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro.oliveira@email.com',
                'phone' => '(21) 98765-4321',
                'cpf' => '456.789.123-00',
                'address' => 'Rua do Comércio, 500',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'postal_code' => '20000-123',
                'active' => true,
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@email.com',
                'phone' => '(31) 98765-4321',
                'cpf' => '789.123.456-00',
                'address' => 'Avenida Brasil, 789',
                'city' => 'Belo Horizonte',
                'state' => 'MG',
                'postal_code' => '30123-456',
                'active' => true,
            ],
            [
                'name' => 'Lucas Ferreira',
                'email' => 'lucas.ferreira@email.com',
                'phone' => '(41) 98765-4321',
                'cpf' => '321.654.987-00',
                'address' => 'Rua XV de Novembro, 1500',
                'city' => 'Curitiba',
                'state' => 'PR',
                'postal_code' => '80020-310',
                'active' => true,
            ],
            [
                'name' => 'Juliana Lima',
                'email' => 'juliana.lima@email.com',
                'phone' => '(51) 98765-4321',
                'cpf' => '654.987.321-00',
                'address' => 'Avenida Independência, 2000',
                'city' => 'Porto Alegre',
                'state' => 'RS',
                'postal_code' => '90035-075',
                'active' => true,
            ],
            [
                'name' => 'Rafael Santos',
                'email' => 'rafael.santos@email.com',
                'phone' => '(81) 98765-4321',
                'cpf' => '147.258.369-00',
                'address' => 'Rua da Aurora, 300',
                'city' => 'Recife',
                'state' => 'PE',
                'postal_code' => '50050-000',
                'active' => true,
            ],
            [
                'name' => 'Carla Rodrigues',
                'email' => 'carla.rodrigues@email.com',
                'phone' => '(71) 98765-4321',
                'cpf' => '369.258.147-00',
                'address' => 'Avenida Oceânica, 1000',
                'city' => 'Salvador',
                'state' => 'BA',
                'postal_code' => '40140-130',
                'active' => true,
            ],
            [
                'name' => 'Fernando Almeida',
                'email' => 'fernando.almeida@email.com',
                'phone' => '(85) 98765-4321',
                'cpf' => '741.852.963-00',
                'address' => 'Rua Barão do Rio Branco, 500',
                'city' => 'Fortaleza',
                'state' => 'CE',
                'postal_code' => '60025-060',
                'active' => true,
            ],
            [
                'name' => 'Beatriz Martins',
                'email' => 'beatriz.martins@email.com',
                'phone' => '(92) 98765-4321',
                'cpf' => '963.852.741-00',
                'address' => 'Avenida Eduardo Ribeiro, 200',
                'city' => 'Manaus',
                'state' => 'AM',
                'postal_code' => '69010-010',
                'active' => true,
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
