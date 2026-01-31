<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\categorie;
use App\Models\product;
use Illuminate\Support\Str;

class SaybaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin
        $admin = User::firstOrCreate([
            'email' => 'admin@local'
        ], [
            'name' => 'Admin',
            'password' => bcrypt('secret123'),
            'role' => 'admin'
        ]);

        // Create vendor
        $vendor = User::firstOrCreate([
            'email' => 'vendeur@local'
        ], [
            'name' => 'Vendeur',
            'password' => bcrypt('secret123'),
            'role' => 'vendeur'
        ]);

        // Create a category
        $cat = categorie::firstOrCreate([
            'name' => 'Vêtements'
        ], [
            'description' => 'Articles textiles',
            'image' => null
        ]);

        // Create sample products
        product::firstOrCreate([
            'name' => 'T-shirt Coton',
            'vendeur_id' => $vendor->id
        ], [
            'categorie_id' => $cat->id,
            'description' => 'T-shirt confortable en coton',
            'price' => 19.99,
            'stock' => 50,
            'image' => 'tshirt.jpg'
        ]);

        product::firstOrCreate([
            'name' => 'Jean Slim',
            'vendeur_id' => $vendor->id
        ], [
            'categorie_id' => $cat->id,
            'description' => 'Jean slim coupe moderne',
            'price' => 49.99,
            'stock' => 30,
            'image' => 'jean.jpg'
        ]);
    }
}
