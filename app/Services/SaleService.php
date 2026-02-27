<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\CashTransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    protected $orderRepo;
    protected $invoiceRepo;
    protected $cashRepo;

    public function __construct(
        OrderRepository $orderRepo,
        InvoiceRepository $invoiceRepo,
        CashTransactionRepository $cashRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->invoiceRepo = $invoiceRepo;
        $this->cashRepo = $cashRepo;
    }
public function sell($data)
{
    return DB::transaction(function () use ($data) {

        $product = Product::findOrFail($data['product_id']);

        $quantity = $data['quantity'];
        $salePrice = $data['sale_price'];

        // ✅ التحقق من توفر المخزون
        if ($product->quantity < $quantity) {
            throw new \Exception('Not enough stock available');
        }

        $total = $quantity * $salePrice;

        // إنشاء الطلب (بيع)
        $order = $this->orderRepo->create([
            'buyer_id'   => auth()->id(),
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'price'      => $salePrice,
            'total'      => $total,
            'status'     => 'completed'
        ]);

        // إنشاء الفاتورة
        $invoice = $this->invoiceRepo->create([
            'order_id'       => $order->id,
            'invoice_number' => 'SALE-' . strtoupper(Str::random(6)),
            'total_amount'   => $total,
            'status'         => 'paid',
            'issued_at'      => now()
        ]);

        // تسجيل حركة نقدية (إيراد)
        $this->cashRepo->create([
            'invoice_id' => $invoice->id,
            'amount'     => $total,
            'type'       => 'income'
        ]);

        // ✅ طرح الكمية من المخزون
        $product->decrement('quantity', $quantity);

        // ✅ تحديث الحالة حسب الكمية المتبقية
        if ($product->quantity == 0) {
            $product->update(['status' => 'sold']);
        } else {
            $product->update(['status' => 'available']);
        }

        return $order->load('product', 'invoice');
    });
}

    public function getSalesInvoices()
{
    return $this->invoiceRepo->getSalesInvoices();
}
}
