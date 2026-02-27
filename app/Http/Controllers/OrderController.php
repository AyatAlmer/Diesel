<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function addOrder(Request $request)
{
    $request->validate([
        'product_id'     => 'required|exists:products,id',
        'quantity'       => 'required|integer|min:1',
        'purchase_price' => 'required|numeric|min:0'
    ]);

    $order = $this->orderService->purchase($request->all());

    return response()->json([
        'message' => 'Purchase completed successfully',
        'data'    => $order
    ]);
}

public function getPurchaseInvoices()
{
    $invoices = $this->orderService->getPurchaseInvoices();

    $formatted = $invoices->map(function ($invoice) {
        return [
            'invoice_number' => $invoice->invoice_number,
            'product_name'   => $invoice->order->product->title,
            'quantity'       => $invoice->order->quantity,
            'purchase_price' => $invoice->order->price,
            'total'          => $invoice->total_amount,
            'issued_at'      => $invoice->issued_at,
        ];
    });

    return response()->json($formatted);
}
}
