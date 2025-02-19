<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts for each status
        $pendingCount       = Order::where('status', 'Pending')->count();
        $preparingCount     = Order::where('status', 'preparing')->count();
        $outForDeliveryCount = Order::where('status', 'Out for Delivery')->count();
        $completedCount     = Order::where('status', 'Completed')->count();
        $canceledCount      = Order::where('status', 'Canceled')->count();

        // Daily Sales - grouping orders by date and summing a 'total_price' field
        $dailySales = Order::select(DB::raw("DATE(created_at) as date"), DB::raw("SUM(final_price_with_discount) as total"))
        ->groupBy(DB::raw("DATE(created_at)"))
        ->orderBy('date', 'ASC')
        ->limit(7)
        ->get();

        // Statistics - count orders by status
        $statistics = Order::select('status', DB::raw("COUNT(*) as count"))
            ->groupBy('status')
            ->get();

        // Total Revenue - similar to daily sales; could be refined further
        $revenue = Order::select(DB::raw("DATE(created_at) as date"), DB::raw("SUM(final_price_with_discount) as total"))
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date', 'ASC')
            ->limit(7)
            ->get();

        // Pass the counts to the view
        return view('admin.dashboard', compact(
            'pendingCount', 
            'preparingCount', 
            'outForDeliveryCount', 
            'completedCount', 
            'canceledCount',
            'dailySales',
            'statistics',
            'revenue'
        ));
    }
}
