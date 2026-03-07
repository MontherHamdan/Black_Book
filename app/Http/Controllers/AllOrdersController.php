<?php

namespace App\Http\Controllers;

use App\Models\Order;

class AllOrdersController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // 🛡️ فقط الأدمن والمشرف والطابع
        if (!$user->isAdmin() && !$user->isSupervisor() && !$user->isPrinter()) {
            abort(403);
        }

        $orders = Order::with(['bookType', 'designer', 'university', 'diploma'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.all_orders.index', compact('orders'));
    }
}
