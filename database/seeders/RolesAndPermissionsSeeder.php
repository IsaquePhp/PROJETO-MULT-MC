<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Produtos
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'manage-stock',
            'view-stock',
            
            // Vendas
            'view-sales',
            'create-sales',
            'edit-sales',
            'cancel-sales',
            'view-sales-report',
            
            // Estoque
            'view-inventory',
            'manage-inventory',
            'view-inventory-report',
            
            // Fluxo de Caixa
            'view-cash-flow',
            'manage-cash-flow',
            'view-cash-flow-report',
            
            // Usuários
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Papéis e Permissões
            'view-roles',
            'manage-roles',
            'view-permissions',
            'manage-permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456')
        ]);

        $admin->assignRole('admin');
    }
}
