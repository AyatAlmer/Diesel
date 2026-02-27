<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
     protected $fillable = [
        'invoice_id',
        'amount',
        'type'
    ];
   public function invoice()
{
    return $this->belongsTo(Invoice::class);
}
}
