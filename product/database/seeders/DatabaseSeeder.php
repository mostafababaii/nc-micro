<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @var ProductSeeder $productSeeder
     */
    protected ProductSeeder $productSeeder;

    /**
     * @param ProductSeeder $productSeeder
     */
    public function __construct(ProductSeeder $productSeeder)
    {
        $this->productSeeder = $productSeeder;
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->productSeeder->run();
    }
}
