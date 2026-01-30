<?php

namespace App\Http\Controllers;

use App\Models\categorie;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Récupérer toutes les catégories
    public function index()
    {
        $categories = categorie::all();
        return response()->json([
            'message' => 'Catégories récupérées avec succès',
            'data' => $categories
        ], 200);
    }

    // Créer une nouvelle catégorie
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $category = categorie::create($validatedData);

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'data' => $category
        ], 201);
    }

    // Récupérer une catégorie spécifique
    public function show($id)
    {
        $category = categorie::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie non trouvée'
            ], 404);
        }

        return response()->json([
            'message' => 'Catégorie récupérée avec succès',
            'data' => $category
        ], 200);
    }

    // Modifier une catégorie
    public function update(Request $request, $id)
    {
        $category = categorie::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie non trouvée'
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $category->update($validatedData);

        return response()->json([
            'message' => 'Catégorie modifiée avec succès',
            'data' => $category
        ], 200);
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        $category = categorie::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie non trouvée'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ], 200);
    }
}
