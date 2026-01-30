<?php

use App\Models\User;
use App\Models\categorie;
use App\Models\product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test users
    $this->admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@test',
        'password' => bcrypt('pass'),
        'role' => 'admin'
    ]);

    $this->vendor = User::create([
        'name' => 'Vendor',
        'email' => 'vendor@test',
        'password' => bcrypt('pass'),
        'role' => 'vendeur'
    ]);

    $this->client = User::create([
        'name' => 'Client',
        'email' => 'client@test',
        'password' => bcrypt('pass'),
        'role' => 'client'
    ]);

    // Create test category
    $this->category = categorie::create([
        'name' => 'Test Category',
        'description' => 'Test'
    ]);
});

// ===== CATEGORIES TESTS =====

test('GET /api/categories is public', function () {
    $response = $this->getJson('/api/categories');
    expect($response->status())->toBe(200);
    expect($response->json('message'))->toBe('Catégories récupérées avec succès');
});

test('POST /api/categories requires admin role', function () {
    // Without token
    $response = $this->postJson('/api/categories', [
        'name' => 'New Category'
    ]);
    expect($response->status())->toBe(401);

    // With vendor token
    $response = $this->actingAs($this->vendor)->postJson('/api/categories', [
        'name' => 'New Category'
    ]);
    expect($response->status())->toBe(403);
});

test('Admin can create category', function () {
    $response = $this->actingAs($this->admin)->postJson('/api/categories', [
        'name' => 'Electronics',
        'description' => 'Electronic devices'
    ]);
    expect($response->status())->toBe(201);
    expect($response->json('data.name'))->toBe('Electronics');
});

test('Admin can update category', function () {
    $response = $this->actingAs($this->admin)->putJson(
        "/api/categories/{$this->category->id}",
        ['name' => 'Updated Category']
    );
    expect($response->status())->toBe(200);
    expect($response->json('data.name'))->toBe('Updated Category');
});

test('Admin can delete category', function () {
    $response = $this->actingAs($this->admin)->deleteJson(
        "/api/categories/{$this->category->id}"
    );
    expect($response->status())->toBe(200);
    expect(categorie::find($this->category->id))->toBeNull();
});

// ===== PRODUCTS TESTS =====

test('GET /api/products is public', function () {
    product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Test Product',
        'price' => 99.99,
        'stock' => 10
    ]);

    $response = $this->getJson('/api/products');
    expect($response->status())->toBe(200);
    expect($response->json('data.data'))->toHaveCount(1);
});

test('Vendor can create product', function () {
    $response = $this->actingAs($this->vendor)->postJson(
        '/api/produits_vendeurs',
        [
            'categorie_id' => $this->category->id,
            'name' => 'Vendor Product',
            'description' => 'A vendor product',
            'price' => 49.99,
            'stock' => 20
        ]
    );
    expect($response->status())->toBe(201);
    expect($response->json('data.vendeur_id'))->toBe($this->vendor->id);
});

test('Client cannot create product', function () {
    $response = $this->actingAs($this->client)->postJson(
        '/api/produits_vendeurs',
        [
            'categorie_id' => $this->category->id,
            'name' => 'Client Product',
            'price' => 29.99,
            'stock' => 5
        ]
    );
    expect($response->status())->toBe(403);
});

test('Vendor can update own product', function () {
    $product = product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Original Name',
        'price' => 99.99,
        'stock' => 10
    ]);

    $response = $this->actingAs($this->vendor)->putJson(
        "/api/produits_vendeurs/{$product->id}",
        ['name' => 'Updated Name']
    );
    expect($response->status())->toBe(200);
    expect($response->json('data.name'))->toBe('Updated Name');
});

test('Vendor cannot update another vendor product', function () {
    $vendor2 = User::create([
        'name' => 'Vendor 2',
        'email' => 'vendor2@test',
        'password' => bcrypt('pass'),
        'role' => 'vendeur'
    ]);

    $product = product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Product',
        'price' => 99.99,
        'stock' => 10
    ]);

    $response = $this->actingAs($vendor2)->putJson(
        "/api/produits_vendeurs/{$product->id}",
        ['name' => 'Hacked']
    );
    expect($response->status())->toBe(403);
});

// ===== STOCK MANAGEMENT TESTS =====

test('Vendor can increment stock', function () {
    $product = product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Product',
        'price' => 99.99,
        'stock' => 10
    ]);

    $response = $this->actingAs($this->vendor)->postJson(
        "/api/produits_vendeurs/{$product->id}/increment-stock",
        ['quantity' => 5]
    );
    expect($response->status())->toBe(200);
    expect($response->json('data.stock'))->toBe(15);
});

test('Vendor can decrement stock', function () {
    $product = product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Product',
        'price' => 99.99,
        'stock' => 10
    ]);

    $response = $this->actingAs($this->vendor)->postJson(
        "/api/produits_vendeurs/{$product->id}/decrement-stock",
        ['quantity' => 3]
    );
    expect($response->status())->toBe(200);
    expect($response->json('data.stock'))->toBe(7);
});

test('Cannot decrement more stock than available', function () {
    $product = product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Product',
        'price' => 99.99,
        'stock' => 10
    ]);

    $response = $this->actingAs($this->vendor)->postJson(
        "/api/produits_vendeurs/{$product->id}/decrement-stock",
        ['quantity' => 20]
    );
    expect($response->status())->toBe(400);
    expect($response->json('message'))->toContain('Stock insuffisant');
});

// ===== FILTERING & SEARCH TESTS =====

test('Filter products by category', function () {
    $cat2 = categorie::create(['name' => 'Category 2']);
    
    product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Product 1',
        'price' => 99.99,
        'stock' => 10
    ]);

    product::create([
        'categorie_id' => $cat2->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Product 2',
        'price' => 49.99,
        'stock' => 5
    ]);

    $response = $this->getJson("/api/products?categorie_id={$this->category->id}");
    expect($response->json('data.data'))->toHaveCount(1);
    expect($response->json('data.data.0.name'))->toBe('Product 1');
});

test('Filter products by price range', function () {
    product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Cheap Product',
        'price' => 10.00,
        'stock' => 10
    ]);

    product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Expensive Product',
        'price' => 500.00,
        'stock' => 5
    ]);

    $response = $this->getJson('/api/products?min_price=50&max_price=200');
    expect($response->json('data.data'))->toHaveCount(0);
});

test('Filter by availability', function () {
    product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'In Stock',
        'price' => 99.99,
        'stock' => 10
    ]);

    product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Out of Stock',
        'price' => 49.99,
        'stock' => 0
    ]);

    $response = $this->getJson('/api/products?available=true');
    expect($response->json('data.data'))->toHaveCount(1);
    expect($response->json('data.data.0.name'))->toBe('In Stock');
});

test('Search products by name', function () {
    product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Gaming Keyboard',
        'price' => 99.99,
        'stock' => 10
    ]);

    product::create([
        'categorie_id' => $this->category->id,
        'vendeur_id' => $this->vendor->id,
        'name' => 'Gaming Mouse',
        'price' => 49.99,
        'stock' => 5
    ]);

    $response = $this->getJson('/api/products?search=Keyboard');
    expect($response->json('data.data'))->toHaveCount(1);
    expect($response->json('data.data.0.name'))->toBe('Gaming Keyboard');
});
