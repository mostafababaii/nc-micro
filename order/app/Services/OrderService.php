<?php

namespace App\Services;

use Exception;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\UpdateProductStockException;
use App\Exceptions\InvalidOrderAmountException;

class OrderService
{
    /**
     * @var Order
     */
    protected Order $orderRepository;

    /**
     * @param Order $orderRepository
     */
    public function __construct(Order $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function create(array $data): array
    {
        // In this case, it is better to use the default isolation level (READ COMMITTED).
        DB::beginTransaction();

        try {
            $result = $this->placeOrder($data);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array
     * @throws InsufficientStockException|UpdateProductStockException|Exception
     */
    private function placeOrder(array $data): array
    {
        $order = $this->createOrder($data['customer_id']);
        $totalAmount = 0;
        $products = [];

        foreach ($data['products'] as $product) {
            $productDetails = $this->getProductDetails($product['id']);
            $this->validateStock($productDetails, $product['quantity']);

            $totalAmount += $productDetails['price'] * $product['quantity'];
            $this->createOrderItem($order->id, $product, $productDetails['price']);

            $products['products'][] = [
                'product_id' => $product['id'],
                'quantity' => $product['quantity']
            ];
        }

        $this->updateProductStock($products);
        $this->updateOrderTotalAmount($order, $totalAmount);

        return ['order_id' => $order->id, 'total_amount' => $totalAmount];
    }

    /**
     * Create a new order.
     *
     * @param int $customerId
     * @return Order
     */
    private function createOrder(int $customerId): Order
    {
        return Order::create(['customer_id' => $customerId, 'total_amount' => 0]);
    }

    /**
     * Get product details from the product service.
     *
     * @param int $productId
     * @return array
     * @throws Exception
     */
    private function getProductDetails(int $productId): array
    {
        $response = Http::get(config('services.product') . "/api/products/{$productId}");
        if ($response->status() == 404) {
            throw new Exception($response->json('error'), 404);
        }
        return $response->json();
    }

    /**
     * Validate if there is enough stock for the product.
     *
     * @param array $productDetails
     * @param int $quantity
     * @throws InsufficientStockException
     */
    private function validateStock(array $productDetails, int $quantity): void
    {
        if ($productDetails['stock'] < $quantity) {
            throw new InsufficientStockException(productId: $productDetails['id']);
        }
    }

    /**
     * Create an order item.
     *
     * @param int $orderId
     * @param array $product
     * @param float $price
     */
    private function createOrderItem(int $orderId, array $product, float $price): void
    {
        OrderItem::create([
            'order_id' => $orderId,
            'product_id' => $product['id'],
            'quantity' => $product['quantity'],
            'price' => $price,
        ]);
    }

    /**
     * Update the product stock.
     *
     * @param array $products
     * @throws InsufficientStockException|UpdateProductStockException
     */
    private function updateProductStock(array $products): void
    {
        $response = Http::post(config('services.product') . "/api/products/stock", $products);
        if ($response->status() == 400) {
            throw new InsufficientStockException($response->json('error'));
        } elseif ($response->status() != 204) {
            throw new UpdateProductStockException($response->json('error'));
        }
    }

    /**
     * Update the total amount of the order.
     *
     * @param Order $order
     * @param float $totalAmount
     * @throws InvalidOrderAmountException
     */
    private function updateOrderTotalAmount(Order $order, float $totalAmount): void
    {
        if ($totalAmount <= 0) {
            throw new InvalidOrderAmountException("Total amount must be greater than zero.");
        }

        $order->total_amount = $totalAmount;
        $order->save();
    }

    /**
     * @param int $customerId
     * @return mixed
     */
    public function getByCustomerId(int $customerId): mixed
    {
        return $this->orderRepository->getByCustomerId($customerId);
    }
}
