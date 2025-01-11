<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

//    public function test_can_create_order()
//    {
//        $response = $this->post('/api/orders', [
//            'customer_id' => 1,
//            'products' => [
//                ['id' => 1, 'quantity' => 1],
//            ],
//        ]);
//
//        $response->assertStatus(201);
//        $this->assertDatabaseHas('orders', ['customer_id' => 1]);
//    }

    public function test_cannot_create_order()
    {
        $response = $this->post('/api/orders', [
            'customer_id' => 1,
            'products' => [
                ['id' => 100000, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(404);
    }

    public function test_can_get_customer_orders()
    {
        $response = $this->get("/api/orders/customer/1");

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }
}
