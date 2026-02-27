<?php

namespace App\Repositories;

use App\Models\Invoice;

class InvoiceRepository
{
    public function create(array $data)
    {
        return Invoice::create($data);
    }

    public function getSalesInvoices()
{
    return Invoice::with([
            'order.product'
        ])
        ->where('invoice_number', 'like', 'SALE-%')
        ->latest()
        ->get();
}

    public function getPurchaseInvoices()
{
    return Invoice::with([
            'order.product'
        ])
        ->where('invoice_number', 'like', 'PUR-%')
        ->latest()
        ->get();
}
}
