<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // dix chiffres au total dont 2 après la virgule
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade'); // clé étrangère vers la table categories
            $table->foreignId('vendeur_id')->constrained('users')->onDelete('cascade'); // clé étrangère vers la table users
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
