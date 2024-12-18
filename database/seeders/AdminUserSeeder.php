<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar empresa padrão se não existir
        $company = Company::firstOrCreate(
            ['document' => '00.000.000/0000-00'],
            [
                'name' => 'Empresa Padrão',
                'email' => 'empresa@padrao.com',
                'phone' => '(00) 0000-0000',
                'address' => 'Endereço Padrão',
                'city' => 'Cidade',
                'state' => 'UF',
                'zip_code' => '00000-000',
                'status' => 'active'
            ]
        );

        // Criar o usuário admin se não existir
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('123456'),
                'company_id' => $company->id
            ]
        );

        // Criar role de admin se não existir
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Atribuir role ao usuário se ainda não tiver
        if (!$user->hasRole('admin')) {
            $user->assignRole($adminRole);
        }
    }
}
