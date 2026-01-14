<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\cart;
use App\Models\order;
use App\Models\product;
use App\Models\order_item;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
        // les fonctions de relation pour le client
    public function orders(){
        return $this->hasMany(order::class);
    }
    public function cart(){
        return $this->hasMany(cart::class);
    }
    // les fonctions de relation pour le vendeur 
    public function products(){
        return $this->hasMany(product::class, 'vendeur_id');
    }
    public function sales(){
        return $this->hasManyThrough(order_item::class, product::class, 'vendeur_id', 'product_id');
    }
}
