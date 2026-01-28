<?php
namespace App\Http\Controllers\Api;

use App\Models\cart;
use App\Models\order;
use App\Models\orderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\CartCalculationService;



class OrderController extends Controller
{
    public function __construct(
        private CartCalculationService $calculationService,
        private StockValidationService $stockService
    ) {}

    /**
     * GET /api/orders - Liste des commandes du client
     */
    public function index(): JsonResponse
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
            ],
        ]);
    }

    /**
     * GET /api/orders/{id} - Détails d'une commande
     */
    public function show(order $order): JsonResponse
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé',
            ], 403);
        }

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'shipping_address' => $order->shipping_address,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ];
                }),
            ],
        ]);
    }

    /**
     * POST /api/orders - Créer une commande depuis le panier
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        // Récupérer le panier actif
        $cart = cart::with('items.product')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre panier est vide',
            ], 422);
        }

        // Valider le stock
        $stockErrors = $this->stockService->validateCartStock($cart->items);
        if (!empty($stockErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant pour certains produits',
                'data' => ['errors' => $stockErrors],
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Créer la commande
            $order = order::create([
                'user_id' => auth()->id(),
                'total_amount' => $this->calculationService->calculateCartTotal($cart),
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'payment_method' => $request->payment_method,
            ]);

            // Créer les items de commande et décrémenter le stock
            foreach ($cart->items as $cartItem) {
                // Créer l'item de commande
                orderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->price,
                ]);

                // Décrémenter le stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Marquer le panier comme converti
            $cart->update(['status' => 'converted']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'data' => $order->load('items.product'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/orders/{id} - Annuler une commande
     */
    public function destroy(order $order): JsonResponse
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé',
            ], 403);
        }

        // Vérifier que la commande peut être annulée
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande ne peut plus être annulée',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Restituer le stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Marquer comme annulée
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commande annulée avec succès',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}