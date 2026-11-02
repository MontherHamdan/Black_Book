<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DesignerAccountingController extends Controller
{
    private $doneStatuses = ['preparing', 'Completed', 'Received', 'out_for_delivery', 'returned'];

    public function index()
    {
        // جلب المصممين والمشرفين الذين لديهم طلبات لتجنب مشكلة N+1
        $designers = User::where(function ($query) {
                $query->where('role', User::ROLE_DESIGNER)
                    ->orWhere(function ($q) {
                        $q->where('role', User::ROLE_SUPERVISOR)
                            ->whereHas('designerOrders');
                    });
            })
            ->with([
                'designerOrders' => function ($query) {
                    $query->whereIn('status', $this->doneStatuses);
                }
            ])
            ->get();

        foreach ($designers as $designer) {
            foreach ($designer->designerOrders as $order) {
                $this->ensureOrderCommission($order, $designer);
            }

            // إجمالي ما تم دفعه للمصمم / المشرف
            $designer->paid_commission = $designer->designerOrders->sum('paid_commission');

            // إجمالي المتبقي (العمولة الكلية للطلب ناقص ما تم دفعه منه)
            $designer->unpaid_commission = $designer->designerOrders
                ->where('is_commission_paid', false)
                ->sum(function ($order) {
                    return $order->designer_commission - $order->paid_commission;
                });
        }

        return view('admin.accounting.index', compact('designers'));
    }

    public function show(User $user)
    {
        if (!$user->isDesigner() && !$user->isSupervisor()) {
            abort(404, 'المستخدم ليس مصمماً أو مشرفاً');
        }

        $allOrders = Order::where('designer_id', $user->id)
            ->whereIn('status', $this->doneStatuses)
            ->get();

        foreach ($allOrders as $order) {
            $this->ensureOrderCommission($order, $user);
        }

        $unpaidOrders = Order::where('designer_id', $user->id)
            ->whereIn('status', $this->doneStatuses)
            ->where('is_commission_paid', false)
            ->orderBy('updated_at', 'desc')
            ->get();

        $paidOrders = Order::where('designer_id', $user->id)
            ->whereIn('status', $this->doneStatuses)
            ->where('paid_commission', '>', 0) // عرض أي طلب اندفع منه جزء أو كل
            ->orderBy('commission_paid_at', 'desc')
            ->get();

        $totalPaid = Order::where('designer_id', $user->id)
            ->whereIn('status', $this->doneStatuses)
            ->sum('paid_commission');

        $totalUnpaid = $unpaidOrders->sum(function ($order) {
            return $order->designer_commission - $order->paid_commission;
        });

        return view('admin.accounting.show', compact('user', 'unpaidOrders', 'paidOrders', 'totalUnpaid', 'totalPaid'));
    }

    // الدفع الكامل (تصفير الحساب)
    public function settle(Request $request, User $user)
    {
        if (!$user->isDesigner() && !$user->isSupervisor()) {
            return back()->with('error', 'المستخدم ليس مصمماً أو مشرفاً.');
        }

        $unpaidOrders = Order::where('designer_id', $user->id)
            ->whereIn('status', $this->doneStatuses)
            ->where('is_commission_paid', false)
            ->get();

        foreach ($unpaidOrders as $order) {
            $this->ensureOrderCommission($order, $user);

            $order->update([
                'paid_commission' => $order->designer_commission, // تسديد كامل القيمة
                'is_commission_paid' => true,
                'commission_paid_at' => now()
            ]);
        }

        return back()->with('success', "تم تسديد الحساب بالكامل بنجاح.");
    }

    // الدفع المخصص (مبلغ معين)
    public function customSettle(Request $request, User $user)
    {
        if (!$user->isDesigner() && !$user->isSupervisor()) {
            return back()->with('error', 'المستخدم ليس مصمماً أو مشرفاً.');
        }

        $request->validate([
            'custom_amount' => 'required|numeric|min:0.1'
        ]);

        $amountToPay = $request->custom_amount;

        // جلب الطلبات الغير مدفوعة (من الأقدم للأحدث عشان نسدد القديم أول)
        $unpaidOrders = Order::where('designer_id', $user->id)
            ->whereIn('status', $this->doneStatuses)
            ->where('is_commission_paid', false)
            ->orderBy('updated_at', 'asc')
            ->get();

        foreach ($unpaidOrders as $order) {
            $this->ensureOrderCommission($order, $user);
        }

        $totalUnpaid = $unpaidOrders->sum(function ($order) {
            return $order->designer_commission - $order->paid_commission;
        });

        if ($amountToPay > $totalUnpaid) {
            return back()->with('error', 'المبلغ المدخل أكبر من الرصيد المعلق!');
        }

        foreach ($unpaidOrders as $order) {
            if ($amountToPay <= 0) {
                break; // خلص المبلغ اللي دفعه الآدمن
            }

            $remainingOnOrder = $order->designer_commission - $order->paid_commission;

            if ($amountToPay >= $remainingOnOrder) {
                // المبلغ بيغطي هذا الطلب بالكامل
                $order->update([
                    'paid_commission' => $order->designer_commission,
                    'is_commission_paid' => true,
                    'commission_paid_at' => now()
                ]);
                $amountToPay -= $remainingOnOrder;
            } else {
                // المبلغ بيغطي جزء من هذا الطلب فقط
                $order->update([
                    'paid_commission' => $order->paid_commission + $amountToPay,
                    'commission_paid_at' => now() // ضفنا هذا السطر عشان يتسجل وقت الدفعة
                ]);
                $amountToPay = 0;
            }
        }

        return back()->with('success', "تم تسجيل الدفعة المخصصة بنجاح وتوزيعها على الطلبات.");
    }

    /**
     * التأكد من احتساب عمولة الطلب إذا كانت NULL
     */
    private function ensureOrderCommission(Order $order, User $user): float
    {
        if ($order->designer_commission !== null && (float) $order->designer_commission > 0) {
            return (float) $order->designer_commission;
        }

        $commission = (float) ($user->base_order_price ?? 0);

        // 1. فحص الزخرفة
        if (!empty($order->book_decorations_id)) {
            $commission += (float) ($user->decoration_price ?? 0);
        }

        // 2. فحص الإهداء المخصص
        if ($order->gift_type === 'custom') {
            $commission += (float) ($user->custom_gift_price ?? 0);
        }

        // 3. فحص الصورة الداخلية
        $additionalIds = $order->additional_image_id;
        if (is_string($additionalIds)) {
            $additionalIds = json_decode($additionalIds, true);
        }
        if (is_array($additionalIds) && !empty($additionalIds)) {
            $commission += (float) ($user->internal_image_price ?? 0);
        }

        $order->designer_commission = $commission;
        $order->saveQuietly();

        return $commission;
    }
}