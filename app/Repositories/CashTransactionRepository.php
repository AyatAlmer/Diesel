<?php

namespace App\Repositories;

use App\Models\CashTransaction;

class CashTransactionRepository
{
    public function create(array $data)
    {
        return CashTransaction::create($data);
    }
}
