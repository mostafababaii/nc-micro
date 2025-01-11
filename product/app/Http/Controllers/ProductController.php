<?php

namespace App\Http\Controllers;

use Exception;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateStockProductRequest;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param StoreProductRequest $request
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());
        return response()->json($product, 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getAll($request->query());
        return response()->json($products);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $products = $this->productService->findById($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage() . 'product id: ' . $id);
            return response()->json([
                'message' => 'Product not found',
                'error' => 'Product not found',
            ], status: 404);
        }
        return response()->json($products);
    }

    /**
     * @param UpdateStockProductRequest $request
     * @return JsonResponse
     */
    public function stock(UpdateStockProductRequest $request): JsonResponse
    {
        $products = $request->validated('products');

        try {
            $this->productService->updateStocks($products);
        } catch (Exception $e) {
            $statusCode = 500;
            if ($e->getCode() >= 400 and $e->getCode() <= 599) {
                $statusCode = $e->getCode();
            }

            return response()->json([
                'message' => 'Stock update failed',
                'error' => $e->getMessage(),
            ], status: $statusCode);
        }

        return response()->json(status: 204);
    }
}
