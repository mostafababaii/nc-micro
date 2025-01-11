<?php

namespace App\Http\Controllers;

use Exception;
use App\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param StoreOrderRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $result = $this->orderService->create($request->validated());
        } catch (Exception $e) {
            $statusCode = 500;
            if ($e->getCode() >= 400 and $e->getCode() <= 599) {
                $statusCode = $e->getCode();
            }

            return response()->json([
                'message' => 'Place order failed',
                'error' => $e->getMessage(),
            ], status: $statusCode);
        }

        return response()->json($result, status: 201);
    }

    /**
     * @param int $customerId
     * @return JsonResponse
     */
    public function customerOrders(int $customerId): JsonResponse
    {
        $orders = $this->orderService->getByCustomerId($customerId);
        return response()->json($orders);
    }
}
