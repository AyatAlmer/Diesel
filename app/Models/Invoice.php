<?php

namespace App\Models;

use App\Models\CashTransaction;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'invoice_number',
        'total_amount',
        'status',
        'issued_at'
    ];

public function order()
{
    return $this->belongsTo(Order::class);
}

public function transactions()
{
    return $this->hasMany(CashTransaction::class);
}
}
