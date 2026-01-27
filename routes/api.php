<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;



// Routes d'authentification

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Route protegée 

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); // deconnexion

    Route::get('/user', function(Request $request){
        return $request->user();
    });
// Routes accessibles uniquement aux vendeurs
    Route::middleware('role:vendeur')->group(function(){
    });
});

// Routes accessibles uniquement aux administrateurs
Route::middleware(['auth:sanctum', 'role:admin'])->group(function(){
        Route::get('admin/users', function(){
            return User::all();
        });
});