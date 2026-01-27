<?php

namespace App\Models;

use App\Models\User;
use App\Models\orderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','total_amount', 'status', 'shipping_address','payment_method'];

    protected $casts = [
        'shipping_address' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function items(){
        return $this->hasMany(orderItem::class);
    }
}
