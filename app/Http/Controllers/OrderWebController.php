<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderWebController extends Controller
{
    public function index()
    {
        return view('admin.order.index');
    }

    /**
     * Fetch orders for DataTable.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchOrders(Request $request)
    {
        $perPage = $request->input('length', 10);
        $page = ($request->input('start', 0) / $perPage) + 1;

        $columnIndex = $request->input('order.0.column'); // Column index
        $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
        $sortDirection = $request->input('order.0.dir') ?? 'desc'; // 'asc' or 'desc'

        // Search filter
        $searchValue = $request->input('search.value');

        $query = Order::with([
            'discountCode',
            'bookType',
            'bookDesign',
            'frontImage',
            'additionalImage',
            'transparentPrinting'
        ]);

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('username_ar', 'like', "%$searchValue%")
                    ->orWhere('username_en', 'like', "%$searchValue%")
                    ->orWhere('governorate', 'like', "%$searchValue%")
                    ->orWhere('address', 'like', "%$searchValue%")
                    ->orWhere('user_phone_number', 'like', "%$searchValue%")
                    ->orWhere('delivery_number_two', 'like', "%$searchValue%")
                    ->orWhere('status', 'like', "%$searchValue%")
                    ->orWhere('final_price_with_discount', 'like', "%$searchValue%");
            });
        }

        // Apply sorting and pagination
        $orders = $query
            ->orderBy($columnName, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedOrders = $orders->getCollection()->map(function ($order) {
            $createdAt = Carbon::parse($order->created_at)->timezone('Asia/Amman');

            return [
                'id' => $order->id,
                'data' => $createdAt->format('d-m-Y h:i A'),
                'username' => $order->username_ar . ' / ' . $order->username_en,
                'order' => $order->bookType->description_en ?? '',
                'governorate' => $order->governorate,
                'address' => $order->address,
                'phone' => $order->user_phone_number,
                'phone2' => $order->delivery_number_two,
                'status' => $order->status,
                'price' => $order->final_price_with_discount,
                'actions' => view('admin.order.partials.actions', compact('order'))->render(),
            ];
        });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => Order::count(),
            'recordsFiltered' => $orders->total(),
            'data' => $formattedOrders,
        ]);
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:orders,id',
            'status' => 'required|in:Pending,preparing,Out for Delivery,Completed,Canceled',
        ]);

        $order = Order::findOrFail($request->id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true]);
    }
}
