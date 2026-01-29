<?php

namespace App\Models;

use App\Models\product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class categorie extends Model
{
    use HasFactory;
    
    protected $fillable =['name', 'description', 'image'];

    public function products(){
        return $this->hasMany(product::class);
    }
}
