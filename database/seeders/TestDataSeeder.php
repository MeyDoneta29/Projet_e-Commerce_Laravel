<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Categorie;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Démarrage du seeder de test...');
        $this->command->newLine();

        // ============================================
        // 1. CRÉER DES CATÉGORIES
        // ============================================
        $this->command->info('📂 Création des catégories...');

        $categories = [
            [
                'name' => 'Électronique',
                'description' => 'Smartphones, ordinateurs, accessoires électroniques',
                'image' => 'categories/electronique.jpg'
            ],
            [
                'name' => 'Vêtements',
                'description' => 'Mode homme, femme et enfant',
                'image' => 'categories/vetements.jpg'
            ],
            [
                'name' => 'Maison & Jardin',
                'description' => 'Décoration, meubles, jardinage',
                'image' => 'categories/maison.jpg'
            ],
            [
                'name' => 'Sport & Loisirs',
                'description' => 'Équipements sportifs et activités de plein air',
                'image' => 'categories/sport.jpg'
            ],
            [
                'name' => 'Livres',
                'description' => 'Romans, BD, magazines, livres techniques',
                'image' => 'categories/livres.jpg'
            ],
        ];

        $createdCategories = collect();
        foreach ($categories as $cat) {
            $category = Categorie::create($cat);
            $createdCategories->push($category);
            $this->command->info("  ✓ {$cat['name']}");
        }

        // ============================================
        // 2. CRÉER DES UTILISATEURS VENDEURS
        // ============================================
        $this->command->newLine();
        $this->command->info('👤 Création des vendeurs...');

        $vendeurs = collect();
        for ($i = 1; $i <= 3; $i++) {
            $vendeur = User::create([
                'name' => "Vendeur $i",
                'email' => "vendeur$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'vendeur',
                'phone' => "+22177123456$i",
                'address' => "$i Rue du Commerce, Dakar, Sénégal",
            ]);
            $vendeurs->push($vendeur);
            $this->command->info("  ✓ {$vendeur->name} ({$vendeur->email})");
        }

        // ============================================
        // 3. CRÉER DES UTILISATEURS CLIENTS
        // ============================================
        $this->command->newLine();
        $this->command->info('👥 Création des clients...');

        $clients = collect();
        for ($i = 1; $i <= 5; $i++) {
            $client = User::create([
                'name' => "Client $i",
                'email' => "client$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'client',
                'phone' => "+22177234567$i",
                'address' => "$i Avenue des Clients, Dakar, Sénégal",
            ]);
            $clients->push($client);
            $this->command->info("  ✓ {$client->name} ({$client->email})");
        }

        // ============================================
        // 4. CRÉER DES PRODUITS
        // ============================================
        $this->command->newLine();
        $this->command->info('🛍️  Création des produits...');

        $productsData = [
            // Électronique
            [
                'category_id' => $createdCategories[0]->id,
                'vendeur_id' => $vendeurs[0]->id,
                'name' => 'iPhone 15 Pro',
                'description' => 'Le dernier iPhone avec puce A17 Pro',
                'price' => 899000,
                'stock' => 50,
                'image' => 'products/iphone15.jpg'
            ],
            [
                'category_id' => $createdCategories[0]->id,
                'vendeur_id' => $vendeurs[0]->id,
                'name' => 'MacBook Pro 16"',
                'description' => 'Ordinateur portable haute performance M3 Max',
                'price' => 1800000,
                'stock' => 20,
                'image' => 'products/macbook.jpg'
            ],
            [
                'category_id' => $createdCategories[0]->id,
                'vendeur_id' => $vendeurs[1]->id,
                'name' => 'AirPods Pro 2',
                'description' => 'Écouteurs sans fil avec réduction de bruit active',
                'price' => 150000,
                'stock' => 100,
                'image' => 'products/airpods.jpg'
            ],
            [
                'category_id' => $createdCategories[0]->id,
                'vendeur_id' => $vendeurs[1]->id,
                'name' => 'Samsung Galaxy S24',
                'description' => 'Smartphone Android haut de gamme',
                'price' => 750000,
                'stock' => 40,
                'image' => 'products/samsung.jpg'
            ],
            // Vêtements
            [
                'category_id' => $createdCategories[1]->id,
                'vendeur_id' => $vendeurs[1]->id,
                'name' => 'T-shirt Premium Coton Bio',
                'description' => 'T-shirt en coton bio de qualité supérieure',
                'price' => 15000,
                'stock' => 200,
                'image' => 'products/tshirt.jpg'
            ],
            [
                'category_id' => $createdCategories[1]->id,
                'vendeur_id' => $vendeurs[2]->id,
                'name' => 'Jean Slim Fit',
                'description' => 'Jean confortable et élégant',
                'price' => 35000,
                'stock' => 150,
                'image' => 'products/jean.jpg'
            ],
            [
                'category_id' => $createdCategories[1]->id,
                'vendeur_id' => $vendeurs[2]->id,
                'name' => 'Sneakers Sport',
                'description' => 'Baskets confortables pour le quotidien',
                'price' => 45000,
                'stock' => 80,
                'image' => 'products/sneakers.jpg'
            ],
            // Maison & Jardin
            [
                'category_id' => $createdCategories[2]->id,
                'vendeur_id' => $vendeurs[2]->id,
                'name' => 'Canapé 3 places',
                'description' => 'Canapé confortable en tissu gris moderne',
                'price' => 450000,
                'stock' => 15,
                'image' => 'products/canape.jpg'
            ],
            [
                'category_id' => $createdCategories[2]->id,
                'vendeur_id' => $vendeurs[0]->id,
                'name' => 'Lampe de bureau LED',
                'description' => 'Lampe moderne avec variateur d\'intensité',
                'price' => 25000,
                'stock' => 80,
                'image' => 'products/lampe.jpg'
            ],
            [
                'category_id' => $createdCategories[2]->id,
                'vendeur_id' => $vendeurs[0]->id,
                'name' => 'Set de 4 Coussins Déco',
                'description' => 'Coussins décoratifs assortis',
                'price' => 18000,
                'stock' => 60,
                'image' => 'products/coussins.jpg'
            ],
            // Sport & Loisirs
            [
                'category_id' => $createdCategories[3]->id,
                'vendeur_id' => $vendeurs[1]->id,
                'name' => 'Tapis de yoga Premium',
                'description' => 'Tapis antidérapant et écologique 6mm',
                'price' => 20000,
                'stock' => 120,
                'image' => 'products/tapis-yoga.jpg'
            ],
            [
                'category_id' => $createdCategories[3]->id,
                'vendeur_id' => $vendeurs[2]->id,
                'name' => 'Haltères réglables 10kg',
                'description' => 'Paire d\'haltères pour musculation à domicile',
                'price' => 45000,
                'stock' => 60,
                'image' => 'products/halteres.jpg'
            ],
            [
                'category_id' => $createdCategories[3]->id,
                'vendeur_id' => $vendeurs[1]->id,
                'name' => 'Ballon de Basketball',
                'description' => 'Ballon officiel taille 7',
                'price' => 12000,
                'stock' => 90,
                'image' => 'products/ballon.jpg'
            ],
            // Livres
            [
                'category_id' => $createdCategories[4]->id,
                'vendeur_id' => $vendeurs[0]->id,
                'name' => 'Clean Code (Robert Martin)',
                'description' => 'Guide pour écrire du code propre et maintenable',
                'price' => 25000,
                'stock' => 50,
                'image' => 'products/clean-code.jpg'
            ],
            [
                'category_id' => $createdCategories[4]->id,
                'vendeur_id' => $vendeurs[0]->id,
                'name' => 'Laravel: Up & Running',
                'description' => 'Guide complet du framework Laravel',
                'price' => 28000,
                'stock' => 40,
                'image' => 'products/laravel-book.jpg'
            ],
        ];

        $createdProducts = collect();
        foreach ($productsData as $product) {
            $createdProduct = Product::create($product);
            $createdProducts->push($createdProduct);
            $this->command->info("  ✓ {$product['name']} - {$product['price']} FCFA");
        }

        // ============================================
        // 5. CRÉER DES PANIERS AVEC DES ITEMS
        // ============================================
        $this->command->newLine();
        $this->command->info('🛒 Création des paniers...');

        // Client 1 - Panier actif avec plusieurs produits électroniques
        $cart1 = Cart::create([
            'user_id' => $clients[0]->id,
            'status' => 'active',
        ]);
        CartItem::create([
            'cart_id' => $cart1->id,
            'product_id' => $createdProducts[0]->id, // iPhone
            'quantity' => 1,
        ]);
        CartItem::create([
            'cart_id' => $cart1->id,
            'product_id' => $createdProducts[2]->id, // AirPods
            'quantity' => 2,
        ]);
        CartItem::create([
            'cart_id' => $cart1->id,
            'product_id' => $createdProducts[4]->id, // T-shirt
            'quantity' => 3,
        ]);
        $this->command->info("  ✓ Panier actif pour {$clients[0]->name} (3 items)");

        // Client 2 - Panier actif avec meubles
        $cart2 = Cart::create([
            'user_id' => $clients[1]->id,
            'status' => 'active',
        ]);
        CartItem::create([
            'cart_id' => $cart2->id,
            'product_id' => $createdProducts[7]->id, // Canapé
            'quantity' => 1,
        ]);
        CartItem::create([
            'cart_id' => $cart2->id,
            'product_id' => $createdProducts[8]->id, // Lampe
            'quantity' => 2,
        ]);
        CartItem::create([
            'cart_id' => $cart2->id,
            'product_id' => $createdProducts[9]->id, // Coussins
            'quantity' => 1,
        ]);
        $this->command->info("  ✓ Panier actif pour {$clients[1]->name} (3 items)");

        // Client 3 - Panier actif avec articles de sport
        $cart3 = Cart::create([
            'user_id' => $clients[2]->id,
            'status' => 'active',
        ]);
        CartItem::create([
            'cart_id' => $cart3->id,
            'product_id' => $createdProducts[10]->id, // Tapis yoga
            'quantity' => 1,
        ]);
        CartItem::create([
            'cart_id' => $cart3->id,
            'product_id' => $createdProducts[11]->id, // Haltères
            'quantity' => 1,
        ]);
        $this->command->info("  ✓ Panier actif pour {$clients[2]->name} (2 items)");

        // Client 4 - Panier vide
        $cart4 = Cart::create([
            'user_id' => $clients[3]->id,
            'status' => 'active',
        ]);
        $this->command->info("  ✓ Panier vide pour {$clients[3]->name}");

        // ============================================
        // 6. CRÉER DES COMMANDES
        // ============================================
        $this->command->newLine();
        $this->command->info('📦 Création des commandes...');

        // Commande 1 - Client 1 (pending)
        $order1 = Order::create([
            'user_id' => $clients[0]->id,
            'total_amount' => 1850000,
            'status' => 'pending',
            'shipping_address' => '1 Avenue des Clients, Dakar, Sénégal',
            'payment_method' => 'carte_bancaire',
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[1]->id, // MacBook
            'quantity' => 1,
            'unit_price' => 1800000,
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[13]->id, // Clean Code
            'quantity' => 2,
            'unit_price' => 25000,
        ]);
        $this->command->info("  ✓ Commande pending pour {$clients[0]->name} (1 850 000 FCFA)");

        // Commande 2 - Client 2 (paid)
        $order2 = Order::create([
            'user_id' => $clients[1]->id,
            'total_amount' => 95000,
            'status' => 'paid',
            'shipping_address' => '2 Avenue des Clients, Dakar, Sénégal',
            'payment_method' => 'mobile_money',
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[4]->id, // T-shirt
            'quantity' => 2,
            'unit_price' => 15000,
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[5]->id, // Jean
            'quantity' => 1,
            'unit_price' => 35000,
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[4]->id, // T-shirt
            'quantity' => 2,
            'unit_price' => 15000,
        ]);
        $this->command->info("  ✓ Commande paid pour {$clients[1]->name} (95 000 FCFA)");

        // Commande 3 - Client 3 (shipped)
        $order3 = Order::create([
            'user_id' => $clients[2]->id,
            'total_amount' => 1049000,
            'status' => 'shipped',
            'shipping_address' => '3 Avenue des Clients, Dakar, Sénégal',
            'payment_method' => 'carte_bancaire',
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $createdProducts[0]->id, // iPhone
            'quantity' => 1,
            'unit_price' => 899000,
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $createdProducts[2]->id, // AirPods
            'quantity' => 1,
            'unit_price' => 150000,
        ]);
        $this->command->info("  ✓ Commande shipped pour {$clients[2]->name} (1 049 000 FCFA)");

        // Commande 4 - Client 4 (delivered)
        $order4 = Order::create([
            'user_id' => $clients[3]->id,
            'total_amount' => 80000,
            'status' => 'delivered',
            'shipping_address' => '4 Avenue des Clients, Dakar, Sénégal',
            'payment_method' => 'virement',
        ]);
        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $createdProducts[5]->id, // Jean
            'quantity' => 1,
            'unit_price' => 35000,
        ]);
        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $createdProducts[11]->id, // Haltères
            'quantity' => 1,
            'unit_price' => 45000,
        ]);
        $this->command->info("  ✓ Commande delivered pour {$clients[3]->name} (80 000 FCFA)");

        // Commande 5 - Client 5 (cancelled)
        $order5 = Order::create([
            'user_id' => $clients[4]->id,
            'total_amount' => 450000,
            'status' => 'cancelled',
            'shipping_address' => '5 Avenue des Clients, Dakar, Sénégal',
            'payment_method' => 'carte_bancaire',
        ]);
        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => $createdProducts[7]->id, // Canapé
            'quantity' => 1,
            'unit_price' => 450000,
        ]);
        $this->command->info("  ✓ Commande cancelled pour {$clients[4]->name} (450 000 FCFA)");

        // Commande 6 - Client 1 (delivered - commande passée)
        $order6 = Order::create([
            'user_id' => $clients[0]->id,
            'total_amount' => 65000,
            'status' => 'delivered',
            'shipping_address' => '1 Avenue des Clients, Dakar, Sénégal',
            'payment_method' => 'mobile_money',
        ]);
        OrderItem::create([
            'order_id' => $order6->id,
            'product_id' => $createdProducts[10]->id, // Tapis yoga
            'quantity' => 1,
            'unit_price' => 20000,
        ]);
        OrderItem::create([
            'order_id' => $order6->id,
            'product_id' => $createdProducts[11]->id, // Haltères
            'quantity' => 1,
            'unit_price' => 45000,
        ]);
        $this->command->info("  ✓ Commande delivered pour {$clients[0]->name} (65 000 FCFA)");

        // ============================================
        // 7. RÉSUMÉ FINAL
        // ============================================
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('✅ DONNÉES DE TEST CRÉÉES AVEC SUCCÈS !');
        $this->command->info('========================================');
        $this->command->newLine();

        $this->command->info("📂 {$createdCategories->count()} catégories créées");
        $this->command->info("👤 {$vendeurs->count()} vendeurs créés");
        $this->command->info("👥 {$clients->count()} clients créés");
        $this->command->info("🛍️  {$createdProducts->count()} produits créés");
        $this->command->info("🛒 4 paniers créés (dont 3 avec items)");
        $this->command->info("📦 6 commandes créées avec différents statuts");

        $this->command->newLine();
        $this->command->info('🔐 Comptes de test:');
        $this->command->info('   Clients: client1@example.com à client5@example.com');
        $this->command->info('   Vendeurs: vendeur1@example.com à vendeur3@example.com');
        $this->command->info('   Mot de passe pour tous: password');

        $this->command->newLine();
        $this->command->info('🚀 Prêt à tester dans Postman !');
    }
}
