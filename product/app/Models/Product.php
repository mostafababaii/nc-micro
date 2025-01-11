<?php

namespace App\Models;

use Exception;
use App\Exceptions\InsufficientStockException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'stock'];


    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     * @throws InsufficientStockException
     */
    public function updateStockWithoutLock(int $productId, int $quantity): void
    {
        $product = Product::where('id', $productId)->first();
        $this->updateStock($product, $quantity);
    }

    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     * @throws InsufficientStockException
     */
    public function updateStockWithLock(int $productId, int $quantity): void
    {
        $lockedProduct = Product::where('id', $productId)->lockForUpdate()->first();
        $this->updateStock($lockedProduct, $quantity);
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return void
     * @throws InsufficientStockException
     */
    private function updateStock(Product $product, int $quantity): void
    {
        if ($product->stock - $quantity < 0) {
            throw new InsufficientStockException(productId: $product->id);
        }

        $product->stock -= $quantity;
        $product->save();
    }
}
