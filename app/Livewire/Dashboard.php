<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\CashSession;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        $startOfToday = Carbon::today()->startOfDay();
        $endOfToday = Carbon::today()->endOfDay();

        // 1. KPI Stats - Range based for indexing
        $totalSalesToday = Sale::whereBetween('created_at', [$startOfToday, $endOfToday])
            ->where('status', '!=', 'voided')
            ->sum('total');

        // Estimate Profit
        $costToday = SaleItem::whereHas('sale', function($q) use ($startOfToday, $endOfToday) {
                $q->whereBetween('created_at', [$startOfToday, $endOfToday])
                  ->where('status', '!=', 'voided');
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->sum(\DB::raw('quantity * products.price_cost'));

        $estimatedProfit = $totalSalesToday - $costToday;

        // Open Sessions
        $openSessionsCount = CashSession::where('status', 'open')
            ->whereHas('user', function($query) {
                $query->whereNull('deleted_at');
            })->count();

        // 2. Low Stock Alerts
        $lowStockProducts = Product::where('is_active', true)
            ->where('manage_stock', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();

        // 3. Recent Sales
        $recentSales = Sale::with(['user', 'customer'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 4. Sales Chart & Sparklines (Last 7 Days)
        $sevenDaysAgo = Carbon::today()->subDays(6)->startOfDay();
        $dailySales = Sale::selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(id) as count')
            ->whereBetween('created_at', [$sevenDaysAgo, $endOfToday])
            ->where('status', '!=', 'voided')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $salesData = [];
        $ordersData = [];
        $salesLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $salesLabels[] = $date->isoFormat('ddd D');
            $salesData[] = (float) ($dailySales[$dateKey]->total ?? 0);
            $ordersData[] = (int) ($dailySales[$dateKey]->count ?? 0);
        }

        // 5. Customers
        $totalCustomers = \App\Models\Customer::where('is_active', true)->count();

        // 6. Top Selling Products (Last 30 Days)
        $topProducts = SaleItem::select('product_name', \DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('sale', function($q) {
                $q->where('created_at', '>=', Carbon::now()->subDays(30))
                  ->where('status', '!=', 'voided');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'totalSalesToday' => $totalSalesToday,
            'estimatedProfit' => $estimatedProfit,
            'openSessionsCount' => $openSessionsCount,
            'lowStockProducts' => $lowStockProducts,
            'recentSales' => $recentSales,
            'salesLabels' => $salesLabels,
            'salesData' => $salesData,
            'ordersData' => $ordersData,
            'totalCustomers' => $totalCustomers,
            'topProducts' => $topProducts
        ])->layout('layouts.app');
    }
}
