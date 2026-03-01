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

    // إضافة فاتورة شراء
    public function addOrder(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.title' => 'required|string',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',  // تعديل هنا
            'products.*.location_id' => 'nullable|exists:locations,id',
            'products.*.description' => 'nullable|string',
            'products.*.condition' => 'nullable|in:new,used',
        ], [
            'products.required' => 'يجب إضافة منتج واحد على الأقل للفاتورة.',
            'products.*.title.required' => 'اسم المنتج مطلوب.',
            'products.*.quantity.required' => 'كمية المنتج مطلوبة.',
            'products.*.price.required' => 'سعر المنتج مطلوب.', // تعديل هنا
            'products.*.location_id.exists' => 'الموقع غير موجود.',
        ]);

        try {
            $invoice = $this->orderService->purchase($request->all());

            return response()->json([
                'message' => 'تمت عملية الشراء بنجاح',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // عرض فواتير الشراء
    public function getPurchaseInvoices()
    {
        $invoices = $this->orderService->getPurchaseInvoices();

        $formatted = $invoices->map(function ($invoice) {
            $products = [
                [
                    'product_name' => $invoice->order->product->title,
                    'quantity' => $invoice->order->quantity,
                    'price' => $invoice->order->price,  // تعديل هنا لتوحيد الاسم
                    'total' => $invoice->total_amount
                ]
            ];

            return [
                'invoice_number' => $invoice->invoice_number,
                'products' => $products,
                'total_amount' => $invoice->total_amount,
                'issued_at' => $invoice->issued_at
            ];
        });

        return response()->json([
            'message' => 'تم جلب فواتير الشراء بنجاح',
            'data' => $formatted
        ]);
    }
}
