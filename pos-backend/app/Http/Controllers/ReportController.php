<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Sales Analytics & Revenue Report
     */
    public function salesReport(Request $request)
    {
        $fromDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $query = Order::whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate])
            ->where('order_status', 'completed');

        $totalRevenue = (clone $query)->sum('grand_total');
        $totalOrders = (clone $query)->count();
        $totalDiscount = (clone $query)->sum('discount');
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // Sales by Payment Method
        $salesByPayment = Order::select('payment_methods.name as payment_method', DB::raw('SUM(orders.grand_total) as total'), DB::raw('COUNT(orders.id) as count'))
            ->leftJoin('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
            ->whereBetween(DB::raw('DATE(orders.created_at)'), [$fromDate, $toDate])
            ->where('orders.order_status', 'completed')
            ->groupBy('payment_methods.name')
            ->get();

        // Top Selling Products in Period
        $topProducts = OrderItem::select(
            'products.product_name',
            'products.image',
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('SUM(order_items.sub_total) as total_sales')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween(DB::raw('DATE(orders.created_at)'), [$fromDate, $toDate])
            ->where('orders.order_status', 'completed')
            ->groupBy('products.id', 'products.product_name', 'products.image')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        // Daily Sales Trend Chart Data
        $dailyTrend = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(grand_total) as revenue'),
            DB::raw('COUNT(id) as orders_count')
        )
            ->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate])
            ->where('order_status', 'completed')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'summary' => [
                'total_revenue' => (float)$totalRevenue,
                'total_orders' => $totalOrders,
                'total_discount' => (float)$totalDiscount,
                'avg_order_value' => (float)$avgOrderValue,
            ],
            'sales_by_payment' => $salesByPayment,
            'top_products' => $topProducts,
            'daily_trend' => $dailyTrend,
        ]);
    }

    /**
     * Orders & Customer Report
     */
    public function ordersReport(Request $request)
    {
        $fromDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $query = Order::with(['customer', 'paymentMethod'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate]);

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->input('order_status'));
        }

        if ($request->filled('txt_search')) {
            $search = $request->input('txt_search');
            $query->where('order_no', 'LIKE', "%{$search}%");
        }

        $totalCount = (clone $query)->count();
        $completedCount = (clone $query)->where('order_status', 'completed')->count();
        $pendingCount = (clone $query)->where('order_status', 'pending')->count();
        $cancelledCount = (clone $query)->where('order_status', 'cancelled')->count();
        $totalAmount = (clone $query)->sum('grand_total');

        $list = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'summary' => [
                'total_count' => $totalCount,
                'completed_count' => $completedCount,
                'pending_count' => $pendingCount,
                'cancelled_count' => $cancelledCount,
                'total_amount' => (float)$totalAmount,
            ],
            'list' => $list,
        ]);
    }

    /**
     * Purchase Orders & Supplier Debt Report
     */
    public function purchaseReport(Request $request)
    {
        $fromDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $query = Purchase::with(['supplier', 'paymentMethod'])
            ->whereBetween(DB::raw('DATE(purchase_date)'), [$fromDate, $toDate]);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $totalPurchases = (clone $query)->count();
        $totalCost = (clone $query)->sum('grand_total');
        $totalPaid = (clone $query)->sum('paid_amount');
        $totalDue = (clone $query)->sum('due_amount');

        $list = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'summary' => [
                'total_purchases' => $totalPurchases,
                'total_cost' => (float)$totalCost,
                'total_paid' => (float)$totalPaid,
                'total_due' => (float)$totalDue,
            ],
            'list' => $list,
        ]);
    }

    /**
     * Expense & Store Overhead Report
     */
    public function expenseReport(Request $request)
    {
        $fromDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $query = Expense::with(['expenseType'])
            ->whereBetween(DB::raw('DATE(expense_date)'), [$fromDate, $toDate]);

        if ($request->filled('expense_type_id')) {
            $query->where('expense_type_id', $request->input('expense_type_id'));
        }

        $totalExpenses = (clone $query)->count();
        $totalAmount = (clone $query)->sum('amount');

        // Breakdown by Expense Type
        $breakdown = Expense::select('expense_types.name as type_name', DB::raw('SUM(expenses.amount) as total_amount'), DB::raw('COUNT(expenses.id) as count'))
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->whereBetween(DB::raw('DATE(expenses.expense_date)'), [$fromDate, $toDate])
            ->groupBy('expense_types.name')
            ->get();

        $list = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'summary' => [
                'total_entries' => $totalExpenses,
                'total_amount' => (float)$totalAmount,
            ],
            'breakdown' => $breakdown,
            'list' => $list,
        ]);
    }
}
