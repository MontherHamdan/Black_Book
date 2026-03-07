<?php

namespace App\Http\Controllers;

use App\Models\Order;

class PrintQueueController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // 🛡️ فقط الأدمن والمشرف والطابع
        if (!$user->isAdmin() && !$user->isSupervisor() && !$user->isPrinter()) {
            abort(403);
        }

        $orders = Order::where('status', 'preparing')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.print_queue.index', compact('orders'));
    }
}
