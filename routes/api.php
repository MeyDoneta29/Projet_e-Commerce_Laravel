<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

// ============================================
// ROUTES D'AUTHENTIFICATION (publiques)
// ============================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// ============================================
// ROUTES PROTÉGÉES (utilisateurs authentifiés)
// ============================================

Route::middleware('auth:sanctum')->group(function () {

    // Route de déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);

    // Informations de l'utilisateur connecté
    Route::get('/user', function(Request $request){
        return $request->user();
    });

    // ============================================
    // ROUTES PANIER (clients)
    // ============================================
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{cartItem}', [CartController::class, 'updateItem']);
        Route::delete('/items/{cartItem}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clear']);
        Route::get('/total', [CartController::class, 'getTotal']);
    });

    // ============================================
    // ROUTES COMMANDES (clients)
    // ============================================
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });
});

// ============================================
// ROUTES VENDEURS (role: vendeur)
// ============================================

Route::middleware(['auth:sanctum', 'role:vendeur'])->group(function(){
    // Routes pour les vendeurs à ajouter ici
    // Ex: gestion des produits, statistiques de ventes, etc.
});

// ============================================
// ROUTES ADMINISTRATEURS (role: admin)
// ============================================

Route::middleware(['auth:sanctum', 'role:admin'])->group(function(){
    // Liste de tous les utilisateurs
    Route::get('admin/users', function(){
        return User::all();
    });
    // Autres routes admin à ajouter ici
});
