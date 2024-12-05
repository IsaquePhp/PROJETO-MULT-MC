<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar e autenticar um usuário para os testes
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        // Criar um produto para os testes
        $this->product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 50
        ]);
    }

    /** @test */
    public function can_list_sales()
    {
        Sale::factory()
            ->has(SaleItem::factory()->count(2))
            ->count(5)
            ->create();

        $response = $this->getJson('/api/sales');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'customer_name',
                        'total_amount',
                        'payment_method',
                        'status',
                        'created_at',
                        'items'
                    ]
                ],
                'current_page',
                'total'
            ]);
    }

    /** @test */
    public function can_create_sale()
    {
        $saleData = [
            'customer_name' => 'Cliente Teste',
            'payment_method' => 'credit_card',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2
                ]
            ],
            'notes' => 'Observações da venda'
        ];

        $response = $this->postJson('/api/sales', $saleData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'sale' => [
                    'id',
                    'customer_name',
                    'total_amount',
                    'items'
                ]
            ]);

        $this->assertDatabaseHas('sales', [
            'customer_name' => 'Cliente Teste',
            'payment_method' => 'credit_card'
        ]);

        $this->assertDatabaseHas('sale_items', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        // Verificar se o estoque foi atualizado
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => 48
        ]);
    }

    /** @test */
    public function cannot_create_sale_with_insufficient_stock()
    {
        $saleData = [
            'customer_name' => 'Cliente Teste',
            'payment_method' => 'credit_card',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 100 // Maior que o estoque disponível
                ]
            ]
        ];

        $response = $this->postJson('/api/sales', $saleData);

        $response->assertStatus(400)
            ->assertJsonFragment([
                'message' => 'Error creating sale',
                'error' => 'Insufficient stock for product: ' . $this->product->name
            ]);

        // Verificar se o estoque permanece inalterado
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => 50
        ]);
    }

    /** @test */
    public function can_show_sale()
    {
        $sale = Sale::factory()
            ->has(SaleItem::factory()->count(2))
            ->create();

        $response = $this->getJson("/api/sales/{$sale->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'customer_name',
                'total_amount',
                'payment_method',
                'status',
                'items' => [
                    '*' => [
                        'id',
                        'product_id',
                        'quantity',
                        'unit_price',
                        'total_price',
                        'product' => [
                            'id',
                            'name'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function can_cancel_sale()
    {
        // Criar uma venda com 2 itens
        $sale = Sale::factory()->create([
            'status' => Sale::STATUS_COMPLETED
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $initialStock = $this->product->stock;

        $response = $this->postJson("/api/sales/{$sale->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Sale cancelled successfully'
            ]);

        // Verificar se a venda foi cancelada
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => Sale::STATUS_CANCELLED
        ]);

        // Verificar se o estoque foi restaurado
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => $initialStock + 2
        ]);
    }

    /** @test */
    public function cannot_cancel_already_cancelled_sale()
    {
        $sale = Sale::factory()->create([
            'status' => Sale::STATUS_CANCELLED
        ]);

        $response = $this->postJson("/api/sales/{$sale->id}/cancel");

        $response->assertStatus(400)
            ->assertJsonFragment([
                'message' => 'Error cancelling sale',
                'error' => 'Sale is already cancelled'
            ]);
    }

    /** @test */
    public function can_get_daily_sales_report()
    {
        // Criar algumas vendas para hoje
        Sale::factory()
            ->has(SaleItem::factory()->count(2))
            ->count(3)
            ->create([
                'status' => Sale::STATUS_COMPLETED
            ]);

        // Criar uma venda cancelada
        Sale::factory()->create([
            'status' => Sale::STATUS_CANCELLED
        ]);

        $response = $this->getJson('/api/sales/daily-report');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'date',
                'total_sales',
                'total_amount',
                'sales_by_payment_method' => [
                    '*' => [
                        'payment_method',
                        'count',
                        'total'
                    ]
                ],
                'top_products' => [
                    '*' => [
                        'product_id',
                        'quantity_sold',
                        'total_revenue',
                        'product'
                    ]
                ]
            ]);

        // Verificar se apenas as vendas completadas são contadas
        $this->assertEquals(3, $response->json('total_sales'));
    }
}
