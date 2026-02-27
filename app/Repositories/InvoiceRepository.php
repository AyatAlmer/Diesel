<?php

namespace App\Repositories;

use App\Models\Invoice;

class InvoiceRepository
{
    public function create(array $data)
    {
        return Invoice::create($data);
    }
}
