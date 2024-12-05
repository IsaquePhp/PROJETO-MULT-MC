<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar e autenticar um usuário para os testes
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function can_list_products()
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'sku',
                        'price',
                        'stock',
                        'created_at'
                    ]
                ],
                'current_page',
                'total'
            ]);
    }

    /** @test */
    public function can_create_product()
    {
        $productData = [
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto teste',
            'sku' => 'SKU123',
            'barcode' => 'BAR123',
            'price' => 99.99,
            'cost_price' => 50.00,
            'stock' => 100,
            'min_stock' => 10,
            'category' => 'Categoria Teste',
            'brand' => 'Marca Teste',
            'unit' => 'un',
            'status' => 'active'
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Produto Teste',
                'sku' => 'SKU123'
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Produto Teste',
            'sku' => 'SKU123'
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'type' => 'in',
            'quantity' => 100,
            'reason' => 'adjustment'
        ]);
    }

    /** @test */
    public function can_show_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => $product->name
            ]);
    }

    /** @test */
    public function can_update_product()
    {
        $product = Product::factory()->create();

        $updateData = [
            'name' => 'Produto Atualizado',
            'price' => 149.99
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Produto Atualizado',
                'price' => 149.99
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produto Atualizado'
        ]);
    }

    /** @test */
    public function cannot_delete_product_with_stock()
    {
        $product = Product::factory()->create([
            'stock' => 10
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Não é possível excluir produto com estoque'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id
        ]);
    }

    /** @test */
    public function can_delete_product_without_stock()
    {
        $product = Product::factory()->create([
            'stock' => 0
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Produto excluído com sucesso'
            ]);

        $this->assertSoftDeleted('products', [
            'id' => $product->id
        ]);
    }

    /** @test */
    public function can_update_stock()
    {
        $product = Product::factory()->create([
            'stock' => 50
        ]);

        $stockData = [
            'quantity' => 10,
            'type' => 'add',
            'reason' => 'purchase',
            'notes' => 'Compra de estoque'
        ];

        $response = $this->postJson("/api/products/{$product->id}/stock", $stockData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Estoque atualizado com sucesso'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 60
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
            'reason' => 'purchase'
        ]);
    }

    /** @test */
    public function cannot_remove_more_than_available_stock()
    {
        $product = Product::factory()->create([
            'stock' => 5
        ]);

        $stockData = [
            'quantity' => 10,
            'type' => 'remove',
            'reason' => 'adjustment',
            'notes' => 'Ajuste de estoque'
        ];

        $response = $this->postJson("/api/products/{$product->id}/stock", $stockData);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Erro ao atualizar estoque',
                'error' => 'Estoque insuficiente'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5
        ]);
    }

    /** @test */
    public function can_get_low_stock_report()
    {
        Product::factory()->create([
            'name' => 'Produto Baixo Estoque',
            'stock' => 5,
            'min_stock' => 10,
            'status' => 'active'
        ]);

        Product::factory()->create([
            'stock' => 20,
            'min_stock' => 10
        ]);

        $response = $this->getJson('/api/products/low-stock-report');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_products',
                'products' => [
                    '*' => [
                        'id',
                        'name',
                        'current_stock',
                        'min_stock',
                        'needed_quantity'
                    ]
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Produto Baixo Estoque',
                'needed_quantity' => 5
            ]);
    }
}
