<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CustomerService
{
    /**
     * @var Customer
     */
    protected Customer $customerRepository;

    /**
     * @param Customer $customerRepository
     */
    public function __construct(Customer $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->customerRepository->create($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        $cacheKey = 'customers_' . $id;
        return Cache::remember($cacheKey, 60, function () use ($id) {
            return $this->customerRepository::where('id', $id)->firstOrFail();
        });
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function getOrders($id): mixed
    {
        $cacheKey = 'customer_orders_' . $id;
        return Cache::remember($cacheKey, 60, function () use ($id) {
            $response = Http::get(config('services.order') . "/api/orders/customer/{$id}");
            return $response->json();
        });
    }
}
