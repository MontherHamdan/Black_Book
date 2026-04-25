<?php

namespace App\Http\Controllers;

use App\Models\Order;

class DeliveryDispatchController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // 🛡️ فقط الأدمن والمشرف والطابع
        if (! $user->isAdmin() && ! $user->isSupervisor() && ! $user->isPrinter()) {
            abort(403);
        }

        $paginatedOrders = Order::with(['bookType', 'university', 'diploma', 'discountCode'])
            ->whereIn('status', ['out_for_delivery', 'Received', 'returned', 'Canceled', 'Printed'])
            ->orderBy('updated_at', 'desc')
            ->paginate(50);

        // Group orders: shared discount code = group, individual = solo
        $deliveryGroups = $paginatedOrders->getCollection()->groupBy(function ($order) {
            return $order->discount_code_id
                ? 'group_'.$order->discount_code_id
                : 'individual_'.$order->id;
        });

        return view('admin.delivery_dispatch.index', compact('deliveryGroups', 'paginatedOrders'));
    }
}
