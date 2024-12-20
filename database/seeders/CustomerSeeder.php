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
                'name' => 'Cliente Teste',
                'email' => 'cliente.teste@email.com',
                'phone' => '11988888888',
                'cpf_cnpj' => '123.456.789-01',
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
                'name' => 'João Silva',
                'email' => 'joao.silva@email.com',
                'phone' => '(11) 98765-4321',
                'cpf_cnpj' => '123.456.789-01',
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
                'email' => 'maria.santos@email.com',
                'phone' => '(11) 98765-1234',
                'cpf_cnpj' => '987.654.321-02',
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
                'name' => 'Tech Solutions Ltda',
                'email' => 'contato@techsolutions.com',
                'phone' => '(11) 3333-4444',
                'cpf_cnpj' => '12.345.678/0001-03',
                'type' => 'company',
                'status' => 'active',
                'company_name' => 'Tech Solutions Ltda',
                'trading_name' => 'Tech Solutions',
                'state_registration' => '123456789',
                'address' => 'Av. Tecnologia, 789',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '08765-432',
                'credit_limit' => 10000.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Ana Oliveira',
                'email' => 'ana.oliveira@email.com',
                'phone' => '(11) 99999-8888',
                'cpf_cnpj' => '456.789.123-04',
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
                'name' => 'Construções ABC Ltda',
                'email' => 'contato@construcoesabc.com',
                'phone' => '(11) 2222-3333',
                'cpf_cnpj' => '98.765.432/0001-05',
                'type' => 'company',
                'status' => 'active',
                'company_name' => 'Construções ABC Ltda',
                'trading_name' => 'ABC Construções',
                'state_registration' => '987654321',
                'address' => 'Av. Industrial, 654',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '05678-901',
                'credit_limit' => 15000.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Carlos Mendes',
                'email' => 'carlos.mendes@email.com',
                'phone' => '(11) 98888-7777',
                'cpf_cnpj' => '789.123.456-06',
                'type' => 'individual',
                'status' => 'active',
                'address' => 'Rua dos Pinheiros, 567',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '04123-789',
                'credit_limit' => 3000.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Fernanda Lima',
                'email' => 'fernanda.lima@email.com',
                'phone' => '(11) 97777-6666',
                'cpf_cnpj' => '321.654.987-07',
                'type' => 'individual',
                'status' => 'active',
                'address' => 'Av. Paulista, 1000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01310-100',
                'credit_limit' => 2500.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Global Trade Importação e Exportação Ltda',
                'email' => 'contato@globaltrade.com',
                'phone' => '(11) 4444-5555',
                'cpf_cnpj' => '45.678.901/0001-08',
                'type' => 'company',
                'status' => 'active',
                'company_name' => 'Global Trade Importação e Exportação Ltda',
                'trading_name' => 'Global Trade',
                'state_registration' => '456789012',
                'address' => 'Av. do Comércio, 2000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '04578-000',
                'credit_limit' => 20000.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Ricardo Santos',
                'email' => 'ricardo.santos@email.com',
                'phone' => '(11) 96666-5555',
                'cpf_cnpj' => '654.987.321-09',
                'type' => 'individual',
                'status' => 'active',
                'address' => 'Rua Augusta, 1500',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01305-100',
                'credit_limit' => 4000.00,
                'company_id' => $company->id
            ],
            [
                'name' => 'Express Logística Ltda',
                'email' => 'contato@expresslog.com',
                'phone' => '(11) 5555-6666',
                'cpf_cnpj' => '78.901.234/0001-10',
                'type' => 'company',
                'status' => 'active',
                'company_name' => 'Express Logística Ltda',
                'trading_name' => 'Express Log',
                'state_registration' => '789012345',
                'address' => 'Av. dos Transportes, 3000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '05789-000',
                'credit_limit' => 25000.00,
                'company_id' => $company->id
            ]
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }
    }
}
