<?php
namespace App\Http\Controllers\Api;

use App\Models\cart;
use App\Models\product;
use App\Models\cartItem;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\CartCalculationService;
use App\Http\Requests\StoreCartItemRequest;



class CartController extends Controller
{
    public function __construct(
        private CartCalculationService $calculationService,
        private StockValidationService $stockService
    ) {}

    /**
     * GET /api/cart - Afficher le panier
     */
    public function index(): JsonResponse
    {
        $cart = $this->getOrCreateCart();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $cart->id,
                'status' => $cart->status,
                'items' => $cart->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'price' => $item->product->price,
                            'image' => $item->product->image,
                        ],
                        'quantity' => $item->quantity,
                        'subtotal' => $this->calculationService->calculateItemSubtotal($item),
                    ];
                }),
                'total' => $this->calculationService->calculateCartTotal($cart),
                'items_count' => $this->calculationService->calculateItemsCount($cart),
            ],
        ]);
    }

    /**
     * POST /api/cart/items - Ajouter au panier
     */
    public function addItem(StoreCartItemRequest $request): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        $product = product::findOrFail($request->product_id);

        // Vérifier le stock
        if (!$this->stockService->hasEnoughStock($product, $request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant',
                'data' => [
                    'available' => $product->stock,
                    'requested' => $request->quantity,
                ],
            ], 422);
        }

        // Vérifier si le produit existe déjà dans le panier
        $existingItem = cartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            // Mettre à jour la quantité
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            if (!$this->stockService->hasEnoughStock($product, $newQuantity)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant pour cette quantité totale',
                ], 422);
            }

            $existingItem->update(['quantity' => $newQuantity]);
            $item = $existingItem;
        } else {
            // Créer un nouvel article
            $item = cartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'data' => [
                'item' => $item->load('product'),
                'cart_total' => $this->calculationService->calculateCartTotal($cart->fresh()),
            ],
        ], 201);
    }

    /**
     * PUT /api/cart/items/{id} - Modifier quantité
     */
    public function updateItem(UpdateCartItemRequest $request, cartItem $cartItem): JsonResponse
    {
        // Vérifier que l'article appartient au panier de l'utilisateur
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé',
            ], 403);
        }

        // Vérifier le stock
        if (!$this->stockService->hasEnoughStock($cartItem->product, $request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant',
                'data' => [
                    'available' => $cartItem->product->stock,
                ],
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Quantité mise à jour',
            'data' => [
                'item' => $cartItem->fresh()->load('product'),
                'cart_total' => $this->calculationService->calculateCartTotal($cartItem->cart),
            ],
        ]);
    }

    /**
     * DELETE /api/cart/items/{id} - Retirer du panier
     */
    public function removeItem(cartItem $cartItem): JsonResponse
    {
        // Vérifier que l'article appartient au panier de l'utilisateur
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé',
            ], 403);
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article retiré du panier',
            'data' => [
                'cart_total' => $this->calculationService->calculateCartTotal($cart->fresh()),
            ],
        ]);
    }

    /**
     * DELETE /api/cart/clear - Vider le panier
     */
    public function clear(): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Panier vidé',
        ]);
    }

    /**
     * GET /api/cart/total - Calculer le total
     */
    public function getTotal(): JsonResponse
    {
        $cart = $this->getOrCreateCart();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $this->calculationService->calculateCartTotal($cart),
                'items_count' => $this->calculationService->calculateItemsCount($cart),
                'summary' => $this->calculationService->generateCartSummary($cart),
            ],
        ]);
    }

    /**
     * Récupère ou crée le panier actif
     */
    private function getOrCreateCart(): cart
    {
        return cart::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'status' => 'active',
            ]
        );
    }
}