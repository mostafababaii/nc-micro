<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product()
    {
        $response = $this->post('/api/products', [
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_can_get_products()
    {
        Product::factory()->count(5)->create();

        $response = $this->get('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }
}
