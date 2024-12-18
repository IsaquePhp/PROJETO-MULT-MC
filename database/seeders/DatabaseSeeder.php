<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\AdditionalCustomersSeeder;
use Database\Seeders\AdditionalProductsSeeder;
use Database\Seeders\MaterialConstrucaoSeeder;
use Database\Seeders\AdminCompanySeeder;
use Database\Seeders\AdditionalClientsSeeder;
use Database\Seeders\StoreSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminCompanySeeder::class,
            StoreSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            MaterialConstrucaoSeeder::class,
            AdditionalProductsSeeder::class,
            CustomerSeeder::class,
            AdditionalClientsSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
        ]);
    }
}
