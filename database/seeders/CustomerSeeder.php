<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Company;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        // Primeiro, vamos garantir que temos uma empresa
        $company = Company::first();
        
        if (!$company) {
            $company = Company::create([
                'name' => 'Empresa Teste',
                'cnpj' => '12345678901234',
                'email' => 'empresa@teste.com',
                'phone' => '(11) 1234-5678',
                'address' => 'Rua Teste, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01234-567'
            ]);
        }

        // Array com dados dos clientes para teste
        $customers = [
            [
                'name' => 'João Silva',
                'email' => 'joao@email.com',
                'phone' => '(11) 98765-4321',
                'cpf_cnpj' => '123.456.789-00',
                'type' => 'individual',
                'status' => 'active',
                'address' => 'Rua das Flores, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01234-567',
                'credit_limit' => 1000.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@email.com',
                'phone' => '(11) 98765-1234',
                'cpf_cnpj' => '987.654.321-00',
                'type' => 'individual',
                'status' => 'active',
                'address' => 'Av. Principal, 456',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '04567-890',
                'credit_limit' => 2000.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Empresa XYZ Ltda',
                'email' => 'contato@xyz.com',
                'phone' => '(11) 3333-4444',
                'cpf_cnpj' => '12.345.678/0001-90',
                'type' => 'company',
                'status' => 'active',
                'company_name' => 'Empresa XYZ Ltda',
                'trading_name' => 'XYZ Comercial',
                'state_registration' => '123456789',
                'address' => 'Av. Comercial, 789',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '08765-432',
                'credit_limit' => 5000.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro@email.com',
                'phone' => '(11) 99999-8888',
                'cpf_cnpj' => '456.789.123-00',
                'type' => 'individual',
                'status' => 'active',
                'address' => 'Rua do Comércio, 321',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '03210-987',
                'credit_limit' => 1500.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'ABC Comércio Ltda',
                'email' => 'contato@abc.com',
                'phone' => '(11) 2222-3333',
                'cpf_cnpj' => '98.765.432/0001-10',
                'type' => 'company',
                'status' => 'active',
                'company_name' => 'ABC Comércio Ltda',
                'trading_name' => 'ABC Comercial',
                'state_registration' => '987654321',
                'address' => 'Av. Industrial, 654',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '07654-321',
                'credit_limit' => 10000.00,
                'company_id' => $company->id
            ]
        ];

        // Criar os clientes
        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }
    }
}
