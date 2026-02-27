<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Product;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected $orderRepo;
    protected $invoiceRepo;

    public function __construct(
        OrderRepository $orderRepo,
        InvoiceRepository $invoiceRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->invoiceRepo = $invoiceRepo;
    }
    public function purchase($data)
{
    return DB::transaction(function () use ($data) {

        $product = Product::findOrFail($data['product_id']);

        $quantity = $data['quantity'];
        $purchasePrice = $data['purchase_price'];
        $total = $quantity * $purchasePrice;

        $order = $this->orderRepo->create([
            'buyer_id'   => auth()->id(),
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'price'      => $purchasePrice,
            'total'      => $total,
            'status'     => 'completed'
        ]);

        $invoice = $this->invoiceRepo->create([
            'order_id'       => $order->id,
            'invoice_number' => 'PUR-' . strtoupper(Str::random(6)),
            'total_amount'   => $total,
            'status'         => 'paid',
            'issued_at'      => now()
        ]);

        CashTransaction::create([
            'invoice_id' => $invoice->id,
            'amount'     => $total,
            'type'       => 'expense'
        ]);

        $product->increment('quantity', $quantity);

        if ($product->quantity > 0) {
            $product->update(['status' => 'available']);
        }

        return $this->orderRepo->withRelations($order);
    });
}

    public function getPurchaseInvoices()
    {
    return $this->invoiceRepo->getPurchaseInvoices();
    }
}
