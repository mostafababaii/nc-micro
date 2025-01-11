<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @var OrderSeeder $orderSeeder
     */
    protected OrderSeeder $orderSeeder;

    /**
     * @param OrderSeeder $orderSeeder
     */
    public function __construct(OrderSeeder $orderSeeder)
    {
        $this->orderSeeder = $orderSeeder;
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->orderSeeder->run();
    }
}
