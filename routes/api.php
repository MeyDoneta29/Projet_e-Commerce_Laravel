<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;



// Routes d'authentification

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Routes publiques pour les catégories et produits (lecture seule)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Route protegée 

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); // deconnexion

    Route::get('/user', function(Request $request){
        return $request->user();
    });
    
    // Routes de gestion des produits
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/products/{id}/increment-stock', [ProductController::class, 'incrementStock']);
    Route::post('/products/{id}/decrement-stock', [ProductController::class, 'decrementStock']);
    
// Routes accessibles uniquement aux vendeurs
    Route::middleware('role:vendeur')->group(function(){
    });
});

// Routes accessibles uniquement aux administrateurs
Route::middleware(['auth:sanctum', 'role:admin'])->group(function(){
        Route::get('admin/users', function(){
            return User::all();
        });
        
        // Routes de gestion des catégories (admin seulement)
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});