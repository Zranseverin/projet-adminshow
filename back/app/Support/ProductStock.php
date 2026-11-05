<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductStockMovement;

class ProductStock
{
    // Call inside a transaction with the product row locked.
    public static function change(Product $product, int $quantity, string $type, string $reason, ?int $userId = null): void
    {
        $before = $product->stock;
        $after = $before + $quantity;
        abort_if($after < 0, 422, 'Stock insuffisant pour ce mouvement.');
        abort_if($after > 10000000, 422, 'La quantité maximale de stock est dépassée.');
        $product->update(['stock' => $after]);
        ProductStockMovement::create(['product_id' => $product->id, 'seller_id' => $product->seller_id,
            'user_id' => $userId, 'quantity' => $quantity, 'stock_before' => $before,
            'stock_after' => $after, 'type' => $type, 'reason' => $reason]);
    }
}
