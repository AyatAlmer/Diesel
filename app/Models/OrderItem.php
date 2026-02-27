<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function order()
{
    return $this->belongsTo(Order::class);
}

public function product()
{
    return $this->belongsTo(Product::class);
}

public function seller()
{
    return $this->belongsTo(User::class, 'seller_id');
}
}
