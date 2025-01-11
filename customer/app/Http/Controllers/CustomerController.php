<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreCustomerRequest;

class CustomerController
{
    /**
     * @var CustomerService
     */
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @param StoreCustomerRequest $request
     * @return JsonResponse
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->validated());
        return response()->json($customer, 201);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $customer = $this->customerService->getById($id);
        return response()->json($customer);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function orders(int $id): JsonResponse
    {
        $orders = $this->customerService->getOrders($id);
        return response()->json($orders);
    }
}
