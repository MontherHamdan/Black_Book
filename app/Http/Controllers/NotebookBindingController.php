<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class NotebookBindingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !(method_exists($user, 'isSupervisor') && $user->isSupervisor()) && !(method_exists($user, 'isPrinter') && $user->isPrinter())) {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }

        $orders = Order::with(['bookType', 'designer', 'discountCode'])
            ->where('status', 'preparing')
            ->whereNotNull('designer_design_file')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        return view('admin.binding.index', compact('orders'));
    }

    /**
     * Mark a single file type as downloaded for one order.
     */
    public function markFileDownloaded(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'file_type' => 'required|in:design,internal,decoration,gift',
        ]);

        $column = 'is_' . $request->file_type . '_downloaded';

        Order::where('id', $request->order_id)->update([$column => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark file type as downloaded for multiple orders (bulk).
     */
    public function bulkMarkDownloaded(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'file_type' => 'required|in:design,internal,decoration,gift',
        ]);

        $column = 'is_' . $request->file_type . '_downloaded';

        Order::whereIn('id', $request->order_ids)->update([$column => true]);

        return response()->json(['success' => true]);
    }
}