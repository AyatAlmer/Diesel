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

    // بيع أكثر من منتج
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|integer|min:1',
            'products.*.sale_price' => 'required|numeric|min:0',
        ], [
            'products.required' => 'يجب إضافة منتج واحد على الأقل للبيع',
            'products.*.product_id.required' => 'المنتج مطلوب',
            'products.*.quantity.required' => 'كمية المنتج مطلوبة',
            'products.*.sale_price.required' => 'سعر البيع مطلوب',
        ]);

        $sale = $this->saleService->sell($request->all());

        return response()->json([
            'message' => 'تم إتمام عملية البيع بنجاح',
            'data'    => $sale
        ]);
    }

    public function getSalesInvoices()
    {
        $sales = $this->saleService->getSalesInvoices();

        return response()->json([
            'message' => 'تم عرض فواتير البيع بنجاح',
            'data'    => $sales
        ]);
    }
}
