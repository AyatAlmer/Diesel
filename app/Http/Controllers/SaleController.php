<?php

namespace App\Http\Controllers;

use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'sale_price' => 'required|numeric|min:0'
        ]);

        $sale = $this->saleService->sell($request->all());

        return response()->json([
            'message' => 'Sale completed successfully',
            'data'    => $sale
        ]);
    }

    public function getSalesInvoices()
{
    $sales = $this->saleService->getSalesInvoices();

    return response()->json([
        'message' => 'Sales invoices retrieved successfully',
        'data'    => $sales
    ]);
}
}
