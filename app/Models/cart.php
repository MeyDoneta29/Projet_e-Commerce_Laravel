<?php

namespace App\Models;

use App\Models\User;
use App\Models\cart_item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status'];

    public function items(){
        return $this->hasMany(cart_item::class); 
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
