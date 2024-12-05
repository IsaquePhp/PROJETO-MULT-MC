<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Limpar o cache do banco de dados
        $this->artisan('migrate:fresh');
        
        // Create roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create permissions
        Permission::create(['name' => 'manage inventory']);
        Permission::create(['name' => 'view inventory']);
        
        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo('manage inventory');
        $admin->givePermissionTo('view inventory');
        
        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo('view inventory');
    }

    protected function tearDown(): void
    {
        // Limpar todas as tabelas após cada teste
        $tables = DB::select('SHOW TABLES');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            $table_array = get_object_vars($table);
            $table_name = array_values($table_array)[0];
            DB::table($table_name)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        parent::tearDown();
    }
}
