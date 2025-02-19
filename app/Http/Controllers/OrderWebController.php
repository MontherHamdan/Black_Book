<?php

namespace App\Http\Controllers;

use App\Models\BookDecoration;
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

    public function show($id)
    {
        $order = Order::with([
            'discountCode',
            'bookType',
            'bookDesign',
            'bookDecoration',
            'frontImage',
            'additionalImage',
            'transparentPrinting',
            'svg',
        ])->findOrFail($id);
        
        return view('admin.order.show', compact('order'));
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
                'data' => $createdAt->format('d M Y, h:i A'),
                'username' => $order->username_ar . ' / ' . $order->username_en,
                'order' => $order->bookType->name_ar ?? '',
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

    // download All BackImages
    public function downloadAllBackImages($orderId)
    {
        // Find the order
        $order = Order::findOrFail($orderId);
    
        // Fetch the back images using the relationship method
        $backImages = $order->backImages();
    
        if ($backImages->isEmpty()) {
            return back()->with('error', 'No back images available');
        }
    
        // Create a zip archive
        $zip = new \ZipArchive();
        $zipFileName = 'back_images_' . $orderId . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);
    
        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
            // Loop through each back image and add it to the zip file
            foreach ($backImages as $image) {
                // Extract the local image path from the URL
                $imagePath = str_replace('http://127.0.0.1:8000/storage/', '', $image->image_path);
                $localPath = storage_path('app/public/' . $imagePath); // Get the full local file path
    
                // Ensure the file exists before adding it to the zip
                if (file_exists($localPath)) {
                    $zip->addFile($localPath, basename($localPath)); // Add the file to the zip with its original filename
                }
            }
            $zip->close();
    
            // Return the zip file as a download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }
    
        return back()->with('error', 'Failed to create ZIP file.');
    }
    
    

}
