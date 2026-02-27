<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Repositories\InvoiceRepository;
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
            $price = $product->price;
            $total = $quantity * $price;

            // إنشاء الطلب
            $order = $this->orderRepo->create([
                'buyer_id'  => auth()->id(),
                'product_id'=> $product->id,
                'quantity'  => $quantity,
                'price'     => $price,
                'total'     => $total,
                'status'    => 'completed'
            ]);

            // إنشاء الفاتورة
            $this->invoiceRepo->create([
                'order_id'       => $order->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
                'total_amount'   => $total,
                'status'         => 'paid',
                'issued_at'      => now()
            ]);

            return $this->orderRepo->withRelations($order);
        });
    }
}
