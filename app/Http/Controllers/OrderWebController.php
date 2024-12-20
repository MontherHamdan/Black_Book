<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Note;
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

        // Status filter
        $statusFilter = $request->input('status'); // Get the selected status filter

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

        // Apply status filter if set
        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
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

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully!',
        ]);
    }

    public function addNote(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'note' => 'required|string|max:1000',
        ]);

        $note = new Note();
        $note->order_id = $request->order_id;
        $note->user_id = auth()->id();
        $note->content = $request->note;

        if ($note->save()) {
            return response()->json([
                'success' => true,
                'note' => [
                    'id' => $note->id,
                    'content' => $note->content,
                    'created_at' => $note->created_at->format('d M Y h:i A'),
                    'user_name' => $note->user->name, // Assuming you have a relationship set up
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to save note.'], 500);
    }

    public function getNotes($orderId)
    {
        $notes = Note::where('order_id', $orderId)
            ->with('user:id,name') // Assuming there's a relationship with User
            ->latest()
            ->get(['id', 'content', 'created_at', 'user_id']);

        return response()->json([
            'notes' => $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'content' => $note->content,
                    'created_at' => $note->created_at->format('d M Y , h:i A'),
                    'user_name' => $note->user->name,
                ];
            }),
        ]);
    }
}
