<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @var CustomerSeeder $customerSeeder
     */
    protected CustomerSeeder $customerSeeder;

    /**
     * @param CustomerSeeder $customerSeeder
     */
    public function __construct(CustomerSeeder $customerSeeder)
    {
        $this->customerSeeder = $customerSeeder;
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->customerSeeder->run();
    }
}
