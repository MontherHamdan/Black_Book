<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Setting;


class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();

        $ordersQuery = Order::query();

        if (!$authUser->isAdmin() && $authUser->isDesigner()) {
            $ordersQuery->where(function ($query) use ($authUser) {
                $query->where('designer_id', $authUser->id)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('status', 'new_order')
                            ->whereNull('designer_id');
                    });
            });
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
        $historyOrders = collect(); // 👈 ضفنا المتغير هون عشان ما يضرب إيرور للآدمن
        $currentPenaltyThreshold = Setting::where('key', 'max_modification_orders')->value('value') ?? 5;
        $designersPenaltyStats = collect();

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

            // 👈 نقلنا استعلام سجل العمولات من الـ Blade للـ Controller
            $historyOrders = $authUser->designerOrders()
                ->whereIn('status', $doneStatuses)
                ->orderBy('updated_at', 'desc')
                ->paginate(5);
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

            $designersPenaltyStats = User::where('role', User::ROLE_DESIGNER)
                ->get()
                ->map(function ($designer) use ($currentPenaltyThreshold) {
                    $modCount = Order::where('designer_id', $designer->id)->where('status', 'needs_modification')->count();
                    $isAutoPenalized = $modCount >= (int) $currentPenaltyThreshold;
                    $isManualPenalized = $designer->penalized_until && $designer->penalized_until->isFuture();

                    return (object) [
                        'id' => $designer->id,
                        'name' => $designer->name,
                        'mod_count' => $modCount,
                        'is_auto_penalized' => $isAutoPenalized,
                        'is_manual_penalized' => $isManualPenalized,
                        'penalized_until' => $designer->penalized_until,
                        'is_penalized' => $isAutoPenalized || $isManualPenalized
                    ];
                });

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
            'totalCommission',
            'historyOrders', // 👈 مررناه للـ View
            'currentPenaltyThreshold',
            'designersPenaltyStats'
        ));
    }

    public function dismissNotes($id)
    {
        $order = Order::findOrFail($id);

        if (auth()->user()->isAdmin() || $order->designer_id == auth()->id()) {
            $order->update(['designer_read_notes' => true]);

            return response()->json([
                'success' => true,
                'message' => 'تم إخفاء الملاحظات من الداشبورد بنجاح'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'غير مصرح لك'], 403);
    }

    public function updatePenaltyThreshold(Request $request)
    {
        abort_if(!auth()->user()->isAdmin() && !(method_exists(auth()->user(), 'isSupervisor') && auth()->user()->isSupervisor()), 403);

        $request->validate([
            'threshold' => 'required|integer|min:1|max:100',
        ]);

        $newThreshold = (int) $request->threshold;

        Setting::updateOrCreate(
            ['key' => 'max_modification_orders'],
            ['value' => $newThreshold]
        );


        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحد الأقصى للتعديلات بنجاح',
            'threshold' => $newThreshold
        ]);
    }

    public function applyDesignerPenalty(Request $request, User $user)
    {
        abort_if(!auth()->user()->isAdmin() && !(method_exists(auth()->user(), 'isSupervisor') && auth()->user()->isSupervisor()), 403);

        if (!$user->isDesigner()) {
            return response()->json(['success' => false, 'message' => 'هذا المستخدم ليس مصمماً'], 400);
        }

        $request->validate([
            'penalty_hours' => 'nullable|integer|min:0',
            'penalty_minutes' => 'nullable|integer|min:0|max:59',
        ]);

        $hours = (int) $request->penalty_hours;
        $minutes = (int) $request->penalty_minutes;

        // حساب إجمالي الدقائق
        $totalMinutes = ($hours * 60) + $minutes;

        $penalizedUntil = null;
        if ($totalMinutes > 0) {
            $penalizedUntil = now()->addMinutes($totalMinutes);
        }

        $user->update(['penalized_until' => $penalizedUntil]);

        $msg = $totalMinutes > 0
            ? "تم تطبيق إيقاف يدوي للمصمم لمدة {$hours} ساعة و {$minutes} دقيقة"
            : "تم رفع الإيقاف اليدوي عن المصمم";

        return response()->json([
            'success' => true,
            'message' => $msg,
            'penalized_until' => $penalizedUntil ? $penalizedUntil->format('Y-m-d H:i:s') : null
        ]);
    }
}