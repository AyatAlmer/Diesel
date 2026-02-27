<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
   public function buyer()
{
    return $this->belongsTo(User::class, 'buyer_id');
}

public function items()
{
    return $this->hasMany(OrderItem::class);
}

public function invoice()
{
    return $this->hasOne(Invoice::class);
}
}
