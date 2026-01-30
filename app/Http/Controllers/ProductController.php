<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Récupérer tous les produits avec filtrage et recherche
    public function index(Request $request)
    {
        $query = product::query();

        // Filtrer par catégorie
        if ($request->has('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        // Filtrer par prix minimum
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Filtrer par prix maximum
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtrer par disponibilité
        if ($request->has('available')) {
            if ($request->available == 'true') {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '=', 0);
            }
        }

        // Rechercher par nom ou description
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15);

        return response()->json([
            'message' => 'Produits récupérés avec succès',
            'data' => $products
        ], 200);
    }

    // Créer un nouveau produit
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'categorie_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
        ]);

        // Ajouter le vendeur_id de l'utilisateur authentifié
        $validatedData['vendeur_id'] = auth()->id();

        $product = product::create($validatedData);

        return response()->json([
            'message' => 'Produit créé avec succès',
            'data' => $product
        ], 201);
    }

    // Récupérer un produit spécifique
    public function show($id)
    {
        $product = product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produit non trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Produit récupéré avec succès',
            'data' => $product
        ], 200);
    }

    // Modifier un produit
    public function update(Request $request, $id)
    {
        $product = product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produit non trouvé'
            ], 404);
        }

        // Vérifier que l'utilisateur est propriétaire du produit
        if ($product->vendeur_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à modifier ce produit'
            ], 403);
        }

        $validatedData = $request->validate([
            'categorie_id' => 'nullable|exists:categories,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|string',
        ]);

        $product->update($validatedData);

        return response()->json([
            'message' => 'Produit modifié avec succès',
            'data' => $product
        ], 200);
    }

    // Supprimer un produit
    public function destroy($id)
    {
        $product = product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produit non trouvé'
            ], 404);
        }

        // Vérifier que l'utilisateur est propriétaire du produit
        if ($product->vendeur_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce produit'
            ], 403);
        }

        $product->delete();

        return response()->json([
            'message' => 'Produit supprimé avec succès'
        ], 200);
    }

    // Incrémenter le stock
    public function incrementStock($id, Request $request)
    {
        $product = product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produit non trouvé'
            ], 404);
        }

        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product->stock += $validatedData['quantity'];
        $product->save();

        return response()->json([
            'message' => 'Stock incrémenté avec succès',
            'data' => $product
        ], 200);
    }

    // Décrémenter le stock
    public function decrementStock($id, Request $request)
    {
        $product = product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produit non trouvé'
            ], 404);
        }

        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($product->stock < $validatedData['quantity']) {
            return response()->json([
                'message' => 'Stock insuffisant'
            ], 400);
        }

        $product->stock -= $validatedData['quantity'];
        $product->save();

        return response()->json([
            'message' => 'Stock décrémenté avec succès',
            'data' => $product
        ], 200);
    }
}
