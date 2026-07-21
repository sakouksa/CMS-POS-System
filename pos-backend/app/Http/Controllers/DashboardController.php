<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard store overview analytics and statistics.
     */
    public function index()
    {
        // 1. Total Metrics
        $totalRevenue = Order::where('order_status', 'completed')->sum('grand_total');
        $totalPurchases = Purchase::sum('grand_total');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenue - $totalPurchases - $totalExpenses;

        // 2. Low stock alert count
        $lowStockCount = Product::whereColumn('quantity', '<=', 'min_stock_alert')->count();

        // 3. Recent Sales (last 5)
        $recentSales = Order::with(['customer'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // 4. Top Selling Products (by quantity)
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.product_name', 'products.price', 'products.image', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.sub_total) as total_revenue'))
            ->groupBy('products.id', 'products.product_name', 'products.price', 'products.image')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // 5. Monthly statistics for line chart (last 6 months)
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        
        $monthlyRevenue = DB::table('orders')
            ->where('order_status', 'completed')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(DB::raw("TO_CHAR(created_at, 'Mon YYYY') as month"), DB::raw('SUM(grand_total) as amount'))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'Mon YYYY'), TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->get();

        $monthlyPurchases = DB::table('purchases')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(DB::raw("TO_CHAR(created_at, 'Mon YYYY') as month"), DB::raw('SUM(grand_total) as amount'))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'Mon YYYY'), TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->get();

        $monthlyExpenses = DB::table('expenses')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(DB::raw("TO_CHAR(created_at, 'Mon YYYY') as month"), DB::raw('SUM(amount) as amount'))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'Mon YYYY'), TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->get();

        // Combine monthly data into unified chart data format
        $chartData = [];
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStr = now()->subMonths($i)->format('M Y');
            $months[] = $monthStr;
            $chartData[$monthStr] = [
                'month' => $monthStr,
                'revenue' => 0,
                'purchases' => 0,
                'expenses' => 0
            ];
        }

        foreach ($monthlyRevenue as $row) {
            if (isset($chartData[$row->month])) {
                $chartData[$row->month]['revenue'] = round((float)$row->amount, 2);
            }
        }
        foreach ($monthlyPurchases as $row) {
            if (isset($chartData[$row->month])) {
                $chartData[$row->month]['purchases'] = round((float)$row->amount, 2);
            }
        }
        foreach ($monthlyExpenses as $row) {
            if (isset($chartData[$row->month])) {
                $chartData[$row->month]['expenses'] = round((float)$row->amount, 2);
            }
        }

        return response()->json([
            'metrics' => [
                'revenue' => round($totalRevenue, 2),
                'purchases' => round($totalPurchases, 2),
                'expenses' => round($totalExpenses, 2),
                'net_profit' => round($netProfit, 2),
                'low_stock_count' => $lowStockCount,
            ],
            'recent_sales' => $recentSales,
            'top_products' => $topProducts,
            'chart_data' => array_values($chartData),
        ]);
    }
}
