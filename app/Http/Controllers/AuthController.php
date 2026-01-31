<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{

    //Fonction d'inscription
    public function register(RegisterRequest $request){
        $validateData = $request->validated();

        // création de l'utilisateur
        $user = User::create([
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'password' => Hash::make($validateData['password']),
            'phone' => $validateData['phone'] ?? null,
            'address' => $validateData['address'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur enregistré avec succès',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);

    }

    //Fonction de connexion

    public function login(LoginRequest $request){
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        //verfication de securité si l'utilisateur existe déja

        if(! $user || ! Hash::check($credentials['password'], $user->password)){
            return response()->json([
                'message' => 'Les identifiants sont incorrects'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    //Fonction de déconnexion
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    //Fonction pour récupérer le profil de l'utilisateur connecté
    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'Profil récupéré avec succès',
            'data' => $request->user()
        ], 200);
    }

}
