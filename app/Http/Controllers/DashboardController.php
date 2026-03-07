<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();

        // 👈 الاستعلام الأساسي للإحصائيات العلوية
        $ordersQuery = Order::query();

        if (!$authUser->isAdmin() && $authUser->isDesigner()) {
            $ordersQuery->where('designer_id', $authUser->id);
        }

        $statusList = [
            'new_order',
            'needs_modification',
            'Pending',
            'Completed',
            'preparing',
            'Received',
            'out_for_delivery',
            'returned',
            'Canceled',
        ];

        $statusCounts = [];
        foreach ($statusList as $status) {
            $statusCounts[$status] = (clone $ordersQuery)->where('status', $status)->count();
        }

        $newOrderCount = $statusCounts['new_order'];
        $needsModificationCount = $statusCounts['needs_modification'];
        $pendingCount = $statusCounts['Pending'];
        $preparingCount = $statusCounts['preparing'];
        $completedCount = $statusCounts['Completed'];
        $outForDeliveryCount = $statusCounts['out_for_delivery'];
        $returnedCount = $statusCounts['returned'] ?? 0;
        $receivedCount = $statusCounts['Received'];
        $canceledCount = $statusCounts['Canceled'];

        $totalOrders = (clone $ordersQuery)->count();

        $orderStatuses = (clone $ordersQuery)
            ->whereNotIn('status', ['Printed', 'out_for_delivery'])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $ordersWithAdditives = (clone $ordersQuery)->where('is_with_additives', 1)->count();
        $ordersWithoutAdditives = (clone $ordersQuery)->where('is_with_additives', 0)->count();

        $topSellingProducts = (clone $ordersQuery)
            ->select('book_type_id', DB::raw('count(*) as total_orders'))
            ->whereNotNull('book_type_id')
            ->groupBy('book_type_id')
            ->orderByDesc('total_orders')
            ->with('bookType')
            ->take(5)
            ->get();

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

        $recentUsers = User::orderBy('id', 'asc')->take(4)->get();

        $today = Carbon::today();

        $designersScoreboard = User::where('role', User::ROLE_DESIGNER)
            ->withCount([
                'designerOrders as total_orders',
                'designerOrders as completed_orders' => function ($q) {
                    $q->where('designer_done', true);
                },
                'designerOrders as today_assigned_orders' => function ($q) use ($today) {
                    $q->whereDate('updated_at', '>=', $today);
                },
                'designerOrders as preparing_orders' => function ($q) {
                    $q->where('status', 'preparing');
                },
            ])
            ->orderByDesc('total_orders')
            ->get();
        $designerNotes = collect();
        $totalCommission = 0;

        // 🟢 1. إذا كان المستخدم مصمم: يرى فقط ملاحظاته وعمولاته
        if (!$authUser->isAdmin() && $authUser->isDesigner()) {

            $designerNotes = Order::where('designer_id', $authUser->id)
                ->where(function ($q) {
                    $q->where('designer_read_notes', false)
                        ->orWhereNull('designer_read_notes');
                })
                ->where(function ($q) {
                    $q->whereNotNull('design_followup_note')->where('design_followup_note', '!=', '')
                        ->orWhereNotNull('binding_followup_note')->where('binding_followup_note', '!=', '')
                        ->orWhereNotNull('notebook_followup_note')->where('notebook_followup_note', '!=', '');
                })
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'username_ar', 'username_en', 'design_followup_note', 'binding_followup_note', 'notebook_followup_note']);

            $doneStatuses = ['preparing', 'Completed', 'Received', 'out_for_delivery', 'returned'];

            $totalCommission = Order::where('designer_id', $authUser->id)
                ->whereIn('status', $doneStatuses)
                ->where('is_commission_paid', false)
                ->get()
                ->sum(function ($order) {
                    return $order->designer_commission - $order->paid_commission;
                });
        }
        // 🟢 2. إذا كان المستخدم آدمن أو مشرف: يرى كل الملاحظات (لكل الطلبات)
        elseif ($authUser->isAdmin() || (method_exists($authUser, 'isSupervisor') && $authUser->isSupervisor())) {

            $designerNotes = Order::where(function ($q) {
                $q->where('designer_read_notes', false)
                    ->orWhereNull('designer_read_notes');
            })
                ->where(function ($q) {
                    $q->whereNotNull('design_followup_note')->where('design_followup_note', '!=', '')
                        ->orWhereNotNull('binding_followup_note')->where('binding_followup_note', '!=', '')
                        ->orWhereNotNull('notebook_followup_note')->where('notebook_followup_note', '!=', '');
                })
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'username_ar', 'username_en', 'design_followup_note', 'binding_followup_note', 'notebook_followup_note']);

        }

        return view('admin.dashboard', compact(
            'newOrderCount',
            'needsModificationCount',
            'pendingCount',
            'preparingCount',
            'outForDeliveryCount',
            'returnedCount',
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

    public function dismissNotes($id)
    {
        $order = Order::findOrFail($id);

        // التحقق إن المصمم هو صاحب الطلب أو المستخدم أدمن
        if (auth()->user()->isAdmin() || $order->designer_id == auth()->id()) {
            $order->update(['designer_read_notes' => true]);

            return response()->json([
                'success' => true,
                'message' => 'تم إخفاء الملاحظات من الداشبورد بنجاح'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'غير مصرح لك'], 403);
    }
}