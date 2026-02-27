<?php
namespace App\Repositories;

use App\Models\CashTransaction;
use App\Models\Product;

class DashboardRepository
{
    public function getFinancialSummary()
    {
        $totalSales = CashTransaction::where('type', 'income')->sum('amount');

        $totalPurchases = CashTransaction::where('type', 'expense')->sum('amount');

        $cashBalance = $totalSales - $totalPurchases;

        $netProfit = $cashBalance;

        $products = Product::select('title', 'quantity')->get();

        return [
            'cash_balance'     => $cashBalance,
            'total_sales'      => $totalSales,
            'total_purchases'  => $totalPurchases,
            'net_profit'       => $netProfit,
            'products'         => $products
        ];
    }
}
