<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 👈 كل الطلبات (نفس الأرقام للـ Admin وللمصمم)
        $ordersQuery = Order::query();

        // ✅ نفس الـ statuses المستخدمة في صفحة الطلبات / updateStatus
        $statusList = [
            'Pending',          // تم التصميم
            'Completed',        // تم الاعتماد
            'preparing',        // قيد التجهيز
            'Received',         // تم التسليم
            'Out for Delivery', // مرتجع
            'Canceled',         // رفض الاستلام
            'error',            // خطأ
        ];

        // نحسب عدد كل حالة مرة واحدة
        $statusCounts = [];
        foreach ($statusList as $status) {
            $statusCounts[$status] = (clone $ordersQuery)
                ->where('status', $status)
                ->count();
        }

        // نربطهم بنفس المتغيّرات الموجودة أصلًا (عشان partials ما تتكسّر)
        $pendingCount        = $statusCounts['Pending'];
        $preparingCount      = $statusCounts['preparing'];
        $completedCount      = $statusCounts['Completed'];
        $outForDeliveryCount = $statusCounts['Out for Delivery'];
        $receivedCount       = $statusCounts['Received'];
        $canceledCount       = $statusCounts['Canceled'];
        $errorCount          = $statusCounts['error'];   // ⭐ الجديد رقم 7

        // إجمالي الطلبات
        $totalOrders = (clone $ordersQuery)->count();

        // توزيع الحالات (للـ charts)
        $orderStatuses = (clone $ordersQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // مع إضافات / بدون إضافات
        $ordersWithAdditives = (clone $ordersQuery)->where('is_with_additives', 1)->count();
        $ordersWithoutAdditives = (clone $ordersQuery)->where('is_with_additives', 0)->count();

        // Top selling products (book_type_id)
        $topSellingProducts = (clone $ordersQuery)
            ->select('book_type_id', DB::raw('count(*) as total_orders'))
            ->whereNotNull('book_type_id')
            ->groupBy('book_type_id')
            ->orderByDesc('total_orders')
            ->with('bookType')
            ->take(5)
            ->get();

        // Group by school_name
        $ordersBySchool = (clone $ordersQuery)
            ->select('school_name', DB::raw('count(*) as total_orders'))
            ->groupBy('school_name')
            ->orderByDesc('total_orders')
            ->get();

        // أول 4 يوزرز (كما هو)
        $recentUsers = User::orderBy('id', 'asc')
            ->take(4)
            ->get();

        $designersScoreboard = User::where('role', User::ROLE_DESIGNER)
            ->withCount([
                // إجمالي الطلبات المعيّنة للمصمم
                'designerOrders as total_orders',

                // الطلبات التي اعتبرنا أن المصمم أنهى شغله فيها
                'designerOrders as completed_orders' => function ($q) {
                    $q->where('designer_done', true);
                },
            ])
            ->orderByDesc('total_orders')
            ->get();



        return view('admin.dashboard', compact(
            'pendingCount',
            'preparingCount',
            'outForDeliveryCount',
            'completedCount',
            'receivedCount',
            'canceledCount',
            'errorCount',             // ⭐ لا تنسى تمريره للـ view
            'orderStatuses',
            'totalOrders',
            'ordersWithAdditives',
            'ordersWithoutAdditives',
            'topSellingProducts',
            'ordersBySchool',
            'recentUsers',
            'designersScoreboard'
        ));
    }
}
