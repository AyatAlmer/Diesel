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

    public function sell(array $data)
    {
        return DB::transaction(function () use ($data) {

            if (!isset($data['products']) || !is_array($data['products']) || count($data['products']) === 0) {
                throw new \Exception('يجب إضافة منتج واحد على الأقل للبيع.');
            }

            $totalInvoice = 0;
            $orderProducts = [];

            foreach ($data['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $salePrice = $item['sale_price'];

                if ($product->quantity < $quantity) {
                    throw new \Exception("الكمية المتوفرة من {$product->title} غير كافية");
                }

                $total = $quantity * $salePrice;

                // إنشاء الطلب لكل منتج
                $order = $this->orderRepo->create([
                    'buyer_id'   => auth()->id(),
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'price'      => $salePrice,
                    'total'      => $total,
                    'status'     => 'completed'
                ]);

                $totalInvoice += $total;

                // خصم الكمية من المخزون
                $product->decrement('quantity', $quantity);
                $product->update([
                    'status' => $product->quantity > 0 ? 'متاح' : 'مباع'
                ]);

                $orderProducts[] = [
                    'product_name' => $product->title,
                    'quantity' => $quantity,
                    'sale_price' => $salePrice,
                    'total' => $total
                ];
            }

            // إنشاء فاتورة واحدة لكل البيع
            $invoice = $this->invoiceRepo->create([
                'order_id'       => $order->id, // آخر order أو حسب تصميمك
                'invoice_number' => 'SALE-' . strtoupper(Str::random(6)),
                'total_amount'   => $totalInvoice,
                'status'         => 'paid',
                'issued_at'      => now()
            ]);

            // تسجيل حركة نقدية مجمعة (إيراد)
            $this->cashRepo->create([
                'invoice_id' => $invoice->id,
                'amount'     => $totalInvoice,
                'type'       => 'income'
            ]);

            return [
                'invoice_number' => $invoice->invoice_number,
                'total_amount'   => $totalInvoice,
                'products'       => $orderProducts,
                'issued_at'      => $invoice->issued_at,
            ];
        });
    }

    public function getSalesInvoices()
    {
        return $this->invoiceRepo->getSalesInvoices();
    }
}
