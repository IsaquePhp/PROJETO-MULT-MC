<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Company;
use App\Models\Inventory;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventoryControllerTest extends TestCase
{
    protected $user;
    protected $store;
    protected $product;
    protected $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        // Limpar tabelas existentes
        Schema::disableForeignKeyConstraints();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('stores')->truncate();
        DB::table('products')->truncate();
        DB::table('inventories')->truncate();
        DB::table('companies')->truncate();
        Schema::enableForeignKeyConstraints();

        // Criar permissões necessárias
        Permission::create(['name' => 'view-stock', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage-stock', 'guard_name' => 'web']);

        // Criar role com permissões
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(['view-stock', 'manage-stock']);

        // Criar usuário de teste
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        // Criar empresa de teste
        $company = Company::create([
            'name' => 'Test Company',
            'cnpj' => '12345678901',
            'email' => 'test@company.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'TS',
            'zip_code' => '12345-678',
            'active' => true
        ]);

        // Criar loja de teste
        $this->store = Store::create([
            'name' => 'Test Store',
            'cnpj' => '98765432101',
            'email' => 'test@store.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'TS',
            'zip_code' => '12345-678',
            'is_matrix' => false,
            'active' => true,
            'company_id' => $company->id
        ]);

        // Criar produto de teste
        $this->product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'sku' => 'TEST-001',
            'price' => 100.00,
            'cost_price' => 80.00,
            'stock' => 0,
            'min_stock' => 5,
            'category' => 'Test Category',
            'brand' => 'Test Brand',
            'status' => 'active',
            'unit' => 'un'
        ]);

        // Autenticar usuário
        Sanctum::actingAs($this->user);
    }

    protected function tearDown(): void
    {
        // Limpar tabelas após os testes
        Schema::disableForeignKeyConstraints();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('stores')->truncate();
        DB::table('products')->truncate();
        DB::table('inventories')->truncate();
        DB::table('companies')->truncate();
        Schema::enableForeignKeyConstraints();

        parent::tearDown();
    }

    public function test_can_list_inventory()
    {
        // Criar alguns itens de inventário
        Inventory::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'min_quantity' => 5,
            'status' => 'active'
        ]);

        $response = $this->getJson('/api/inventory');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'store_id',
                                'product_id',
                                'quantity',
                                'min_quantity',
                                'status'
                            ]
                        ]
                    ]
                ]);
    }

    public function test_can_create_inventory()
    {
        $data = [
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'min_quantity' => 5,
            'location' => 'A1'
        ];

        $response = $this->postJson('/api/inventory', $data);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Inventory item created successfully'
                ]);
    }

    public function test_can_show_inventory()
    {
        $inventory = Inventory::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'min_quantity' => 5,
            'status' => 'active'
        ]);

        $response = $this->getJson("/api/inventory/{$inventory->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'store_id',
                        'product_id',
                        'quantity',
                        'min_quantity',
                        'status',
                        'product',
                        'store'
                    ]
                ]);
    }

    public function test_can_update_stock()
    {
        $inventory = Inventory::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'min_quantity' => 5,
            'status' => 'active'
        ]);

        $data = [
            'quantity' => 5,
            'type' => 'increase',
            'notes' => 'Stock adjustment'
        ];

        $response = $this->postJson("/api/inventory/{$inventory->id}/update-stock", $data);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Stock updated successfully'
                ]);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 15
        ]);
    }

    public function test_can_transfer_stock()
    {
        $inventory = Inventory::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'min_quantity' => 5,
            'status' => 'active'
        ]);

        $store2 = Store::create([
            'name' => 'Test Store 2',
            'cnpj' => '12345678902',
            'email' => 'test2@store.com',
            'phone' => '0987654321',
            'address' => 'Test Address 2',
            'city' => 'Test City 2',
            'state' => 'TS',
            'zip_code' => '12345-679',
            'is_matrix' => false,
            'active' => true,
            'company_id' => $this->store->company_id
        ]);

        $data = [
            'from_store_id' => $this->store->id,
            'to_store_id' => $store2->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'notes' => 'Stock transfer'
        ];

        $response = $this->postJson("/api/inventory/transfer-stock", $data);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Stock transferred successfully'
                ]);

        // Verificar se o estoque foi atualizado corretamente na origem
        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 5
        ]);

        // Verificar se o novo estoque foi criado no destino
        $this->assertDatabaseHas('inventories', [
            'store_id' => $store2->id,
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);
    }

    public function test_cannot_transfer_more_stock_than_available()
    {
        $inventory = Inventory::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'min_quantity' => 5,
            'status' => 'active'
        ]);

        $store2 = Store::create([
            'name' => 'Test Store 2',
            'cnpj' => '12345678902',
            'email' => 'test2@store.com',
            'phone' => '0987654321',
            'address' => 'Test Address 2',
            'city' => 'Test City 2',
            'state' => 'TS',
            'zip_code' => '12345-679',
            'is_matrix' => false,
            'active' => true,
            'company_id' => $this->store->company_id
        ]);

        $data = [
            'from_store_id' => $this->store->id,
            'to_store_id' => $store2->id,
            'product_id' => $this->product->id,
            'quantity' => 15, // Tentando transferir mais do que o disponível
            'notes' => 'Stock transfer'
        ];

        $response = $this->postJson("/api/inventory/transfer-stock", $data);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Insufficient stock in source store'
                ]);

        // Verificar se o estoque permanece inalterado
        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 10
        ]);
    }

    public function test_cannot_update_stock_with_negative_quantity()
    {
        $inventory = Inventory::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'min_quantity' => 5,
            'status' => 'active'
        ]);

        $data = [
            'quantity' => -15,
            'type' => 'decrease',
            'notes' => 'Stock adjustment'
        ];

        $response = $this->postJson("/api/inventory/{$inventory->id}/update-stock", $data);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => [
                        'quantity' => ['The quantity must be at least 0.']
                    ]
                ]);

        // Verificar se o estoque permanece inalterado
        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 10
        ]);
    }

    public function test_cannot_access_nonexistent_inventory()
    {
        $nonexistentId = 99999;

        $response = $this->getJson("/api/inventory/{$nonexistentId}");

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Inventory item not found'
                ]);
    }

    public function test_cannot_access_inventory_without_permission()
    {
        // Criar um usuário sem permissões
        $userWithoutPermission = User::factory()->create();
        Sanctum::actingAs($userWithoutPermission);

        $inventory = Inventory::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'min_quantity' => 5,
            'status' => 'active'
        ]);

        // Tentar listar inventário
        $response = $this->getJson('/api/inventory');
        $response->assertStatus(403);

        // Tentar ver detalhes do inventário
        $response = $this->getJson("/api/inventory/{$inventory->id}");
        $response->assertStatus(403);

        // Tentar atualizar estoque
        $response = $this->postJson("/api/inventory/{$inventory->id}/update-stock", [
            'quantity' => 5,
            'type' => 'increase',
            'notes' => 'Stock adjustment'
        ]);
        $response->assertStatus(403);

        // Tentar transferir estoque
        $response = $this->postJson("/api/inventory/transfer-stock", [
            'from_store_id' => $this->store->id,
            'to_store_id' => 2,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'notes' => 'Stock transfer'
        ]);
        $response->assertStatus(403);
    }
}
