<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory()->count(50)->create()->each(function ($order) {
            OrderItem::factory()->count(3)->create([
                'order_id' => $order->id,
                'product_id' => rand(1, 1000),
                'quantity' => rand(1, 100),
                'price' => rand(1, 100),
            ]);
        });
    }
}
