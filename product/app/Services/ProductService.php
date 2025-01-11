<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductService
{
    /**
     * @var Product
     */
    protected Product $productRepository;

    /**
     * @param Product $productRepository
     */
    public function __construct(Product $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return Product::create($data);
    }

    /**
     * @param array $query
     * @return mixed
     */
    public function getAll(array $query): mixed
    {
        $cacheKey = 'products_' . md5(json_encode($query));
        return Cache::remember($cacheKey, 60, function () use ($query) {
            return Product::paginate(10);
        });
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findById(int $id): mixed
    {
        $cacheKey = 'products_' . $id;
        return Cache::remember($cacheKey, 60, function () use ($id) {
            return $this->productRepository::where('id', $id)->firstOrFail();
        });
    }

    /**
     * @param array $products
     * @return void
     * @throws Exception
     */
    public function updateStocks(array $products): void
    {
        DB::beginTransaction();
        try {
            DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

            // Because we handle 1000 requests per second, our service is considered high-traffic.
            // Therefore, it is advisable to use the REPEATABLE READ isolation level.
            // With the REPEATABLE READ isolation level, there is no need to use SELECT FOR UPDATE.
            // If concurrency issues arise, the transaction will not be committed
            // under the REPEATABLE READ isolation level, and everything will be rolled back,
            // causing the system to return an error. This behavior is due to MVCC
            // (Multi-Version Concurrency Control) on the REPEATABLE READ isolation level.

            // SELECT FOR UPDATE is not recommended in high-traffic systems because it places
            // an exclusive lock on the selected rows. this exclusive lock can lead to significant
            // performance issues, including increased latency and higher system RAM usage,
            // as it prevents other transactions from accessing the locked rows until the current
            // transaction is completed.

            foreach ($products as $product) {
                $this->productRepository->updateStockWithoutLock($product['product_id'], $product['quantity']);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
