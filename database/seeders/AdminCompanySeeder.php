<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminCompanySeeder extends Seeder
{
    public function run()
    {
        // Criar a empresa principal
        $companyId = DB::table('companies')->insertGetId([
            'name' => 'Casa de Material de Construção Silva',
            'document' => '12.345.678/0001-90',
            'email' => 'contato@construsilva.com.br',
            'phone' => '(11) 3333-4444',
            'address' => 'Av. Principal, 1000',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01000-000',
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Criar o usuário administrador
        DB::table('users')->insert([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
            'company_id' => $companyId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
