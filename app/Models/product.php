<?php

namespace App\Models;

use App\Models\User;
use App\Models\categorie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =['categorie_id', 'vendeur_id', 'name', 'description', 'price', 'stock', 'image'];

    public function categorie(){
        return $this->belongsTo(categorie::class, 'categorie_id');
    }

    public function vendeur(){
        return $this->belongsTo(User::class, 'vendeur_id');
    }
}
