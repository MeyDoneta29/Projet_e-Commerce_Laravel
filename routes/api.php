<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\AuthController; // ← COMMENTE CETTE LIGNE

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

/*
// ===== ROUTES DE LUCE (commentées temporairement) =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
*/

// ===== ROUTE DE TEST POUR L'AUTH =====
// En attendant que Luce finisse, crée un token de test
Route::post('/login', function() {
    return response()->json([
        'success' => true,
        'data' => [
            'user' => ['id' => 1, 'name' => 'Test User'],
            'token' => '1|test-token-temporaire'
        ]
    ]);
});

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    
    /*
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function(Request $request){
        return $request->user();
    });
    */

    // ===== TES ROUTES PANIER =====
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{cartItem}', [CartController::class, 'updateItem']);
        Route::delete('/items/{cartItem}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clear']);
        Route::get('/total', [CartController::class, 'getTotal']);
    });

    // ===== TES ROUTES COMMANDES =====
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });
});