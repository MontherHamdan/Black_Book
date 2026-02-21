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
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();

        // 👈 الاستعلام الأساسي للإحصائيات العلوية (Cards & Charts)
        $ordersQuery = Order::query();

        // 🛡️ 🔴 الإضافة: حصر الكروت العلوية بطلبات المصمم نفسه فقط (إذا لم يكن أدمن)
        if (!$authUser->isAdmin() && $authUser->isDesigner()) {
            $ordersQuery->where('designer_id', $authUser->id);
        }

        // ✅ نفس الـ statuses المستخدمة في صفحة الطلبات / updateStatus
        $statusList = [
            'new_order', // 👈 طلب جديد (ضفناها هون)
            'needs_modification', // 👈 يوجد تعديل (ضفناها هون)
            'Pending',
            'Completed',
            'preparing',
            'Received',
            'Out for Delivery',
            'Canceled',
        ];

        // نحسب عدد كل حالة مرة واحدة (رح تنحسب حسب الفلتر اللي فوق)
        $statusCounts = [];
        foreach ($statusList as $status) {
            $statusCounts[$status] = (clone $ordersQuery)
                ->where('status', $status)
                ->count();
        }

        // نربطهم بنفس المتغيّرات الموجودة أصلًا
        $newOrderCount = $statusCounts['new_order'];
        $needsModificationCount = $statusCounts['needs_modification'];
        $pendingCount = $statusCounts['Pending'];
        $preparingCount = $statusCounts['preparing'];
        $completedCount = $statusCounts['Completed'];
        $outForDeliveryCount = $statusCounts['Out for Delivery'];
        $receivedCount = $statusCounts['Received'];
        $canceledCount = $statusCounts['Canceled'];

        // إجمالي الطلبات (الشخصية للمصمم، أو الكلية للأدمن)
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

        // ✅ Orders by School (جامعة / دبلوم)
        $ordersBySchool = (clone $ordersQuery)
            ->leftJoin('universities', 'orders.university_id', '=', 'universities.id')
            ->leftJoin('diplomas', 'orders.diploma_id', '=', 'diplomas.id')
            ->selectRaw("
                CASE 
                    WHEN orders.user_type = 'university' THEN universities.name
                    WHEN orders.user_type = 'diploma'    THEN diplomas.name
                    ELSE 'غير محدد'
                END AS school_label,
                COUNT(*) AS total_orders
            ")
            ->groupBy('school_label')
            ->orderByDesc('total_orders')
            ->get();

        // ==========================================
        // 🌟 القسم العام (بيظهر للجميع بدون فلاتر شخصية)
        // ==========================================

        // أول 4 يوزرز (كما هو)
        $recentUsers = User::orderBy('id', 'asc')
            ->take(4)
            ->get();

        // Scoreboard (يظهر للجميع لزيادة التنافس)
        $designersScoreboard = User::where('role', User::ROLE_DESIGNER)
            ->withCount([
                'designerOrders as total_orders',
                'designerOrders as completed_orders' => function ($q) {
                    $q->where('designer_done', true);
                },
            ])
            ->orderByDesc('total_orders')
            ->get();

        $designerNotes = collect();
        $totalCommission = 0;

        if (!$authUser->isAdmin() && $authUser->isDesigner()) {
            $designerNotes = Order::where('designer_id', $authUser->id)
                ->whereNotNull('design_followup_note')
                ->where('design_followup_note', '!=', '')
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'username_ar', 'username_en', 'design_followup_note']);

            $doneStatuses = ['preparing', 'Completed', 'Received', 'Out for Delivery'];

            $totalCommission = Order::where('designer_id', $authUser->id)
                ->whereIn('status', $doneStatuses)
                ->sum('designer_commission');
        }
        return view('admin.dashboard', compact(
            'newOrderCount',
            'needsModificationCount',
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
            'recentUsers',
            'designersScoreboard',
            'designerNotes',
            'totalCommission'
        ));
    }
}
