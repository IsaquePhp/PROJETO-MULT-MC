<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar o usuário admin se não existir
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('123456'),
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
