<?php

namespace App\Services;

use App\Models\cart;
use App\Models\cartItem;



class CartCalculationService
{
    /**
     * Calcule le total du panier
     */
    public function calculateCartTotal(cart $cart): float
    {
        return $cart->items->sum(function (cartItem $item) {
            return $item->product->price * $item->quantity;
        });
    }

    /**
     * Calcule le sous-total d'un article
     */
    public function calculateItemSubtotal(cartItem $item): float
    {
        return $item->product->price * $item->quantity;
    }

    /**
     * Calcule le nombre total d'articles
     */
    public function calculateItemsCount(cart $cart): int
    {
        return $cart->items->sum('quantity');
    }

    /**
     * Génère un résumé détaillé du panier
     */
    public function generateCartSummary(cart $cart): array
    {
        $items = $cart->items->map(function (cartItem $item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->product->price,
                'subtotal' => $this->calculateItemSubtotal($item),
            ];
        });

        return [
            'items' => $items,
            'items_count' => $this->calculateItemsCount($cart),
            'total' => $this->calculateCartTotal($cart),
        ];
    }
}