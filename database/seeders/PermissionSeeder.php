<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Company;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Create default company
        $company = Company::create([
            'name' => 'Empresa Padrão',
            'cnpj' => '00.000.000/0000-00',
            'email' => 'contato@empresa.com',
            'phone' => '(00) 0000-0000',
            'address' => 'Endereço Padrão',
            'city' => 'Cidade',
            'state' => 'UF',
            'zip_code' => '00000-000',
            'active' => true
        ]);

        // Create default permissions
        $permissions = [
            // Product permissions
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'manage-stock',
            'view-stock',
            
            // Sales permissions
            'view-sales',
            'create-sales',
            'cancel-sales',
            
            // Cash flow permissions
            'view-cash-flow',
            'create-cash-flow',
            
            // Report permissions
            'view-reports',
            
            // User management permissions
            'manage-users',
            'manage-roles',
            'manage-permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'slug' => \Illuminate\Support\Str::slug($permission),
                'module' => explode('-', $permission)[1] ?? 'general'
            ]);
        }

        // Create default roles
        $roles = [
            'admin' => [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Administrador do sistema',
                'is_admin' => true,
                'permissions' => $permissions
            ],
            'manager' => [
                'name' => 'Gerente',
                'slug' => 'manager',
                'description' => 'Gerente da loja',
                'is_admin' => false,
                'permissions' => [
                    'view-products', 'create-products', 'edit-products',
                    'manage-stock', 'view-stock',
                    'view-sales', 'create-sales', 'cancel-sales',
                    'view-cash-flow', 'create-cash-flow',
                    'view-reports'
                ]
            ],
            'seller' => [
                'name' => 'Vendedor',
                'slug' => 'seller',
                'description' => 'Vendedor da loja',
                'is_admin' => false,
                'permissions' => [
                    'view-products',
                    'view-stock',
                    'view-sales', 'create-sales'
                ]
            ]
        ];

        foreach ($roles as $key => $roleData) {
            $role = Role::create([
                'company_id' => $company->id,
                'name' => $roleData['name'],
                'slug' => $roleData['slug'],
                'description' => $roleData['description'],
                'is_admin' => $roleData['is_admin']
            ]);

            // Attach permissions to role
            $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
            $role->permissions()->attach($permissions);
        }
    }
}
