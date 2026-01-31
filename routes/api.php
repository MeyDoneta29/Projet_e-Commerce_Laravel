<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;


// ROUTES D'AUTHENTIFICATION (publiques)

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


// ROUTES PROTÉGÉES (utilisateurs authentifiés)
// Routes publiques pour les catégories et produits (lecture seule)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Route protegée 

Route::middleware('auth:sanctum')->group(function () {

    // Route de déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);

    // Informations de l'utilisateur connecté
        Route::get('/user', [AuthController::class, 'profile']);

    // ROUTES PANIER (clients)

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{cartItem}', [CartController::class, 'updateItem']);
        Route::delete('/items/{cartItem}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clear']);
        Route::get('/total', [CartController::class, 'getTotal']);
    });

    // ROUTES COMMANDES (clients)

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });
});


// Routes accessibles aux vendeurs et administrateurs (préfixe produits_vendeurs)
Route::prefix('produits_vendeurs')->middleware(['auth:sanctum','role:vendeur,admin'])->group(function(){
    // Routes de gestion des produits par vendeurs/admin
    Route::post('/', [ProductController::class, 'store']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::post('/{id}/increment-stock', [ProductController::class, 'incrementStock']);
    Route::post('/{id}/decrement-stock', [ProductController::class, 'decrementStock']);
});

// Routes accessibles uniquement aux administrateurs
Route::middleware(['auth:sanctum', 'role:admin'])->group(function(){
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
        Route::get('admin/users', function(){
            return User::all();
        });
});
