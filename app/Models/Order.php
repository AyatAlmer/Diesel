<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
    'buyer_id',
    'status',
    'total'
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}

