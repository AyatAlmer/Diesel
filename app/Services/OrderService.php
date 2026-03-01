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

            if (!isset($data['products']) || !is_array($data['products']) || count($data['products']) === 0) {
                throw new \Exception('يجب أن تحتوي الفاتورة على منتج واحد على الأقل.');
            }

            $totalInvoice = 0;
            $orderProducts = [];

            foreach ($data['products'] as $item) {
                $title = $item['title'] ?? null;
                $quantity = $item['quantity'] ?? null;
                $price = $item['price'] ?? null;
                $location_id = $item['location_id'] ?? null;
                $description = $item['description'] ?? null;
                $condition = $item['condition'] ?? 'new';

                if (!$title || !$quantity || !$price) {
                    throw new \Exception("المنتج {$title} يحتوي على بيانات ناقصة (الاسم، الكمية أو السعر).");
                }

                // نبحث إذا المنتج موجود مسبقًا حسب الاسم
                $product = Product::where('title', $title)->first();

                if ($product) {
                    // تحديث الكمية والسعر والوصف والموقع
                    $product->increment('quantity', $quantity);
                    $product->update([
                        'price' => $price,
                        'description' => $description ?? $product->description,
                        'condition' => $condition,
                        'location_id' => $location_id ?? $product->location_id,
                        'status' => 'متاح'
                    ]);
                } else {
                    // إنشاء المنتج الجديد
                    $product = Product::create([
                        'title' => $title,
                        'description' => $description,
                        'condition' => $condition,
                        'location_id' => $location_id,
                        'quantity' => $quantity,
                        'price'  => $price,
                        'status' => 'متاح',
                        'user_id' => auth()->id()
                    ]);
                }

                // إنشاء الطلب لكل منتج
                $order = $this->orderRepo->create([
                    'buyer_id' => auth()->id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price,
                    'status' => 'completed'
                ]);

                $totalInvoice += $quantity * $price;

                $orderProducts[] = [
                    'product_name' => $product->title,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price
                ];
            }

            // إنشاء الفاتورة
            $invoice = $this->invoiceRepo->create([
                'order_id' => $order->id,
                'invoice_number' => 'PUR-' . strtoupper(Str::random(6)),
                'total_amount' => $totalInvoice,
                'status' => 'paid',
                'issued_at' => now()
            ]);

            // تسجيل حركة نقدية
            CashTransaction::create([
                'invoice_id' => $invoice->id,
                'amount' => $totalInvoice,
                'type' => 'expense'
            ]);

            return [
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $totalInvoice,
                'products' => $orderProducts,
                'issued_at' => $invoice->issued_at,
            ];
        });
    }

    public function getPurchaseInvoices()
    {
        return $this->invoiceRepo->getPurchaseInvoices();
    }
}
