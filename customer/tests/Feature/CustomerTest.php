<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_customer()
    {
        $response = $this->post('/api/customers', [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('customers', ['email' => 'test@example.com']);
    }

    public function test_can_get_customer()
    {
        $customer = Customer::factory()->create();
        $response = $this->get("/api/customers/{$customer->id}");

        $response->assertStatus(200);
        $response->assertJson(['id' => $customer->id]);
    }

    public function test_can_get_customer_orders()
    {
        $customer = Customer::factory()->create();
        $response = $this->get("/api/customers/{$customer->id}/orders");

        $response->assertStatus(200);
        $response->assertJson([]);
    }
}
