<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use DB;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts for each status
        $pendingCount       = Order::where('status', 'Pending')->count();
        $preparingCount     = Order::where('status', 'preparing')->count();
        $completedCount     = Order::where('status', 'Completed')->count();
        $outForDeliveryCount = Order::where('status', 'Out for Delivery')->count();
        $receivedCount = Order::where('status', 'Received')->count();
        $canceledCount      = Order::where('status', 'Canceled')->count();
    
        // Get total order count
        $totalOrders = Order::count();
    
        // Order status counts
        $orderStatuses = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
    
        // Orders with additives
        $ordersWithAdditives = Order::where('is_with_additives', 1)->count();
        $ordersWithoutAdditives = Order::where('is_with_additives', 0)->count();
    
        // Get top-selling products based on book_type_id
        $topSellingProducts = Order::select('book_type_id', DB::raw('count(*) as total_orders'))
            ->groupBy('book_type_id')
            ->orderByDesc('total_orders')
            ->with('bookType') // Eager load bookType relation
            ->take(5) // Get the top 5 selling book types
            ->get();
        
        // Group orders by school_name (university)
        $ordersBySchool = Order::select('school_name', DB::raw('count(*) as total_orders'))
        ->groupBy('school_name')
        ->orderByDesc('total_orders')
        ->get();
    
        // ---Fetch 4 first users ---
        $recentUsers = User::orderBy('id', 'asc')->take(4)->get();

        // Pass the counts to the view
        return view('admin.dashboard', compact(
            'pendingCount', 
            'preparingCount', 
            'outForDeliveryCount', 
            'completedCount', 
            'receivedCount',
            'canceledCount',
            'orderStatuses',
            'totalOrders',
            'ordersWithAdditives',
            'ordersWithoutAdditives',
            'topSellingProducts',
            'ordersBySchool',
            'recentUsers'
        ));
    }
    
}
