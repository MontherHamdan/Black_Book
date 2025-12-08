<?php

namespace App\Http\Controllers;

use App\Models\BookDecoration;
use Carbon\Carbon;
use App\Models\Note;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserImage;
class OrderWebController extends Controller
{
    public function index()
    {
        $designers = User::where('role', User::ROLE_DESIGNER)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.order.index', compact('designers'));
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
            'notes.user',
        ])->findOrFail($id);

        $decorations = BookDecoration::orderBy('id')
            ->get(['id', 'name', 'image']);

        return view('admin.order.show', compact('order', 'decorations'));
    }



    /**
     * Fetch orders for DataTable (server-side).
     */
    public function fetchOrders(Request $request)
    {
        $perPage = $request->input('length', 10);
        $page    = ($request->input('start', 0) / $perPage) + 1;

        $columnIndex   = $request->input('order.0.column');
        $columnName    = $request->input('columns')[$columnIndex]['data'] ?? 'id';
        $sortDirection = $request->input('order.0.dir') ?? 'desc';

        $searchValue     = $request->input('search.value');
        $statusFilter    = $request->input('status');
        $additivesFilter = $request->input('additives'); // with_additives / with_out_additives
        $dateFrom        = $request->input('date_from');
        $dateTo          = $request->input('date_to');

        $query = Order::with([
            'discountCode',
            'bookType',
            'bookDesign',
            'frontImage',
            'additionalImage',
            'transparentPrinting',
            'designer',
        ]);

        // 🔎 بحث عام
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('username_ar', 'like', "%{$searchValue}%")
                    ->orWhere('username_en', 'like', "%{$searchValue}%")
                    ->orWhere('governorate', 'like', "%{$searchValue}%")
                    ->orWhere('address', 'like', "%{$searchValue}%")
                    ->orWhere('user_phone_number', 'like', "%{$searchValue}%")
                    ->orWhere('delivery_number_two', 'like', "%{$searchValue}%")
                    ->orWhere('status', 'like', "%{$searchValue}%")
                    ->orWhere('final_price_with_discount', 'like', "%{$searchValue}%")
                    ->orWhere('school_name', 'like', "%{$searchValue}%");
            });
        }

        // 🎯 فلتر الحالة
        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        // ✅ فلتر الإضافات (Notes)
        if ($additivesFilter === 'with_additives') {
            // الطلبات اللي عليها Notes
            $query->whereHas('notes');
        } elseif ($additivesFilter === 'with_out_additives') {
            // الطلبات اللي ما عليها Notes
            $query->whereDoesntHave('notes');
        }

        // 📅 فلاتر التاريخ
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // ☎️ حساب أرقام الهواتف المكررة (ضمن الفلاتر الحالية)
        $duplicatePhones = (clone $query)
            ->select('user_phone_number')
            ->whereNotNull('user_phone_number')
            ->groupBy('user_phone_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_phone_number')
            ->toArray();

        // ترتيب + Pagination
        $orders = $query
            ->orderBy($columnName, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedOrders = $orders->getCollection()->map(function ($order) use ($duplicatePhones) {
            $createdAt = Carbon::parse($order->created_at)->timezone('Asia/Amman');

            return [
                'id'                 => $order->id,
                'data'               => $createdAt->format('d-m-Y, h:i A'),
                'status_created_diff' => $createdAt->diffForHumans(),
                'username'           => $order->username_ar . ' / ' . $order->username_en,
                'order'              => $order->bookType->name_ar ?? '',
                'governorate'        => $order->governorate,
                'address'            => $order->address,
                'school_name'        => $order->school_name,
                'phone'              => $order->user_phone_number,
                'phone2'             => $order->delivery_number_two,
                'status'             => $order->status,
                'price'              => $order->final_price_with_discount,
                'has_notes'          => Note::where('order_id', $order->id)->exists(),
                'is_duplicate_phone' => in_array($order->user_phone_number, $duplicatePhones),

                'designer' => $order->designer ? [
                    'id'   => $order->designer->id,
                    'name' => $order->designer->name,
                ] : null,

                'actions'            => view('admin.order.partials.actions', compact('order'))->render(),
            ];
        });

        return response()->json([
            'draw'            => $request->input('draw'),
            'recordsTotal'    => Order::count(),
            'recordsFiltered' => $orders->total(),
            'data'            => $formattedOrders,
        ]);
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:orders,id',
            'status' => 'required|in:Pending,preparing,Completed,Out for Delivery,Received,Canceled,error',
        ]);

        /** @var \App\Models\User $user */
        $user  = $request->user();
        $order = Order::findOrFail($request->id);

        // 🛡️ التحقق من الصلاحيات
        if (! $user->isAdmin()) {
            // لو مش أدمن لازم يكون مصمم + هو نفسه المعيَّن على الطلب
            if (! $user->isDesigner() || $order->designer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتحديث حالة هذا الطلب.',
                ], 403);
            }
        }

        $newStatus = $request->status;

        // ✅ الحالات اللي نعتبر عندها شغل المصمم "منجَز"
        $designerDoneStatuses = [
            'Completed',        // تم الاعتماد
            'Received',         // تم التسليم
            'Out for Delivery', // مرتجع
            'Canceled',         // رفض الإستلام
        ];

        // تحديث حالة الطلب
        $order->status = $newStatus;

        // أول مرة يدخل الطلب في حالة من الحالات المنجَزة للمصمم
        if (
            in_array($newStatus, $designerDoneStatuses, true) &&
            ! $order->designer_done &&               // ما كان محسوب منجَز قبل
            ! is_null($order->designer_id)           // الطلب فعليًا مع مصمم
        ) {
            $order->designer_done    = true;
            $order->designer_done_at = now();
        }

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
            'note'     => 'required|string|max:1000',
        ]);

        $note            = new Note();
        $note->order_id  = $request->order_id;
        $note->user_id   = auth()->id();
        $note->content   = $request->note;

        if ($note->save()) {
            return response()->json([
                'success' => true,
                'note'    => [
                    'id'         => $note->id,
                    'content'    => $note->content,
                    'created_at' => $note->created_at->format('d M Y h:i A'),
                    'user_name'  => $note->user->name,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to save note.'], 500);
    }

    public function getNotes($orderId)
    {
        $notes = Note::where('order_id', $orderId)
            ->with('user:id,name')
            ->latest()
            ->get(['id', 'content', 'created_at', 'user_id']);

        return response()->json([
            'notes' => $notes->map(function ($note) {
                return [
                    'id'         => $note->id,
                    'content'    => $note->content,
                    'created_at' => $note->created_at->format('d M Y , h:i A'),
                    'user_name'  => $note->user->name,
                ];
            }),
        ]);
    }

    // تحميل كل الصور الخلفية لطلب معين
    public function downloadAllBackImages($orderId)
    {
        $order = Order::findOrFail($orderId);
        $backImages = $order->backImages(); // Collection راجعة من الميثود في الموديل

        if ($backImages->isEmpty()) {
            return back()->with('error', 'لا توجد صور خلفية متاحة لهذا الطلب.');
        }

        $zipFileName = 'back_images_' . $orderId . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        // تأكد إن فولدر storage/app/public موجود
        $zipDir = dirname($zipFilePath);
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        // لو في ملف قديم بنفس الاسم، احذفه
        if (file_exists($zipFilePath)) {
            @unlink($zipFilePath);
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'فشل إنشاء ملف ZIP.');
        }

        $tempFiles = [];

        foreach ($backImages as $img) {

            $path = $img->image_path;

            // 1) لو الصورة URL كامل
            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {

                $imageContent = @file_get_contents($path);

                if ($imageContent === false) {
                    // ما قدر يقرأ الصورة من URL → تجاهلها
                    continue;
                }

                $fileName = basename(parse_url($path, PHP_URL_PATH)) ?: ('image_' . $img->id . '.jpg');

                // نخزنها مؤقتًا في storage/app/tmp/
                $tmpDir = storage_path('app/tmp');
                if (!is_dir($tmpDir)) {
                    mkdir($tmpDir, 0755, true);
                }

                $tempPath = $tmpDir . '/' . uniqid('img_', true) . '_' . $fileName;

                file_put_contents($tempPath, $imageContent);

                // نضيفها للـ ZIP بجوا الاسم البسيط
                $zip->addFile($tempPath, $fileName);

                // نجهزها عشان نحذفها بعد ما نخلص تجهيز الـ ZIP
                $tempFiles[] = $tempPath;
            }
            // 2) لو مخزّنة كمسار محلي داخل storage/app/public
            else {

                // لو جاي على شكل /storage/user_images/xxx.jpg
                if (\Illuminate\Support\Str::startsWith($path, ['/storage/'])) {
                    $relative = ltrim(str_replace('/storage/', '', $path), '/');
                    $localPath = storage_path('app/public/' . $relative);
                }
                // لو جاي user_images/xxx.jpg
                else {
                    $localPath = storage_path('app/public/' . ltrim($path, '/'));
                }

                if (file_exists($localPath)) {
                    $zip->addFile($localPath, basename($localPath));
                }
            }
        }

        $zip->close();

        // نحذف كل الملفات المؤقتة اللي نزلناها من الـ URLs
        foreach ($tempFiles as $tmp) {
            @unlink($tmp);
        }

        // نرسل ملف الـ ZIP للمستخدم، و Laravel يحذف الـ ZIP بعد الإرسال
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }





    /**
     * Export filtered orders as CSV.
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'status'    => $request->get('status'),
            'additives' => $request->get('additives'),
            'search'    => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to'   => $request->get('date_to'),
        ];

        $fileName = 'orders-' . now()->format('Y-m-d_H-i-s') . '.csv';

        return Excel::download(
            new OrdersExport($filters),
            $fileName,
            ExcelFormat::CSV,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    public function downloadAllAdditionalImages($orderId)
    {
        $order = Order::with('additionalImages.userImage')->findOrFail($orderId);
        $images = $order->additionalImages;

        if ($images->isEmpty()) {
            return back()->with('error', 'No additional images available');
        }

        $zip         = new \ZipArchive();
        $zipFileName = 'additional_images_' . $orderId . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

            foreach ($images as $img) {
                if (!$img->userImage || !$img->userImage->image_path) {
                    continue;
                }

                $path = $img->userImage->image_path;

                // لو الصورة URL خارجي
                if (Str::startsWith($path, ['http://', 'https://'])) {
                    try {
                        $contents = @file_get_contents($path);
                        if ($contents === false) {
                            continue;
                        }

                        $fileName = basename(parse_url($path, PHP_URL_PATH)) ?: ('image_' . $img->id . '.jpg');
                        $tempPath = storage_path('app/tmp_' . $fileName);
                        file_put_contents($tempPath, $contents);

                        $zip->addFile($tempPath, $fileName);
                    } catch (\Throwable $e) {
                        continue;
                    }
                } else {
                    // صورة مرفوعة ومحفوظة في storage/user_images
                    $localPath = storage_path('app/public/user_images/' . $path);
                    if (file_exists($localPath)) {
                        $zip->addFile($localPath, basename($localPath));
                    }
                }
            }

            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Failed to create ZIP file.');
    }

    public function updateDesigner(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'designer_id' => 'nullable|exists:users,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $user  = $request->user();

        // ✅ لو مو Admin
        if (!$user->isAdmin()) {

            // لازم يكون Designer أصلاً
            if (!$user->isDesigner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتعديل المصمم.',
                ], 403);
            }

            // الطلب معيّن على مصمم آخر → ممنوع يلمسه
            if ($order->designer_id && $order->designer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الطلب معيّن لمصمم آخر.',
                ], 403);
            }

            // المصمم العادي يسمح له فقط بتعيين نفسه على الطلب
            if ((int) $request->designer_id !== (int) $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'يمكنك فقط تعيين نفسك كمصمم لهذا الطلب.',
                ], 403);
            }
        }

        // 📝 حفظ الـ designer_id (Admin يقدر يعيّن أي مصمم أو يفرّغ)
        $order->designer_id = $request->designer_id ?: null;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المصمم بنجاح.',
        ]);
    }
    public function updateBinding(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 🛡️ فقط أدمن أو ديزاينر
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            abort(403, 'غير مصرح لك بتعديل تجليد الدفتر.');
        }

        $order = Order::with('bookDecoration')->findOrFail($id);

        // ✅ فاليديشين
        $validated = $request->validate([
            'is_with_additives'          => ['nullable'], // checkbox
            'is_sponge'                  => ['nullable'], // checkbox
            'gift_title'                 => ['nullable', 'string', 'max:1000'],
            'internal_image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:20480'],
            'transparent_printing_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:20480'],

            // ⬅️ حقل النص الجديد لاسم الزخرفة
            'book_decoration_name'       => ['nullable', 'string', 'max:255'],
            'binding_followup_note'      => ['nullable', 'string', 'max:5000'],
        ]);

        // 🔘 مع إضافات
        $order->is_with_additives = $request->boolean('is_with_additives');

      

        // 🧽 إسفنج
        $order->is_sponge = $request->boolean('is_sponge');

        // 📝 العبارة على الدفتر
        $order->gift_title = $request->input('gift_title');

        // 📝 تعديل نص الزخرفة (تعديل الـ BookDecoration نفسه)
        if ($request->filled('book_decoration_name') && $order->bookDecoration) {
            $order->bookDecoration->name = $request->input('book_decoration_name');
            $order->bookDecoration->save();
        }

        // 🖼️ رفع / تغيير الصورة الداخلية
        if ($request->hasFile('internal_image')) {
            $file = $request->file('internal_image');

            $timestamp = time();
            $original  = $file->getClientOriginalName();
            $imageName = $timestamp . '_' . $original;

            $file->storeAs('user_images', $imageName, 'public');

            $userImage = UserImage::create([
                'image_path' => $imageName,
            ]);

            $order->internal_image_id = $userImage->id;
        }

        // 🖼️ رفع / تغيير صورة الطباعة الشفافة
        if ($request->hasFile('transparent_printing_image')) {
            $file = $request->file('transparent_printing_image');

            $timestamp = time();
            $original  = $file->getClientOriginalName();
            $imageName = $timestamp . '_' . $original;

            $file->storeAs('user_images', $imageName, 'public');

            $userImage = UserImage::create([
                'image_path' => $imageName,
            ]);

            $order->transparent_printing_id = $userImage->id;
        }
        $order->binding_followup_note = $request->input('binding_followup_note');
        $order->save();

        return redirect()
            ->route('orders.show', $order->id)
            ->with('success', 'تم تحديث تجليد الدفتر بنجاح.');
    }

    public function updateDeliveryFollowup(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 🛡️ نفس منطق الصلاحيات: بس أدمن أو ديزاينر
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            abort(403, 'غير مصرح لك بتعديل ملاحظات التوصيل.');
        }

        $request->validate([
            'delivery_followup_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $order = Order::findOrFail($id);
        $order->delivery_followup_note = $request->input('delivery_followup_note');
        $order->save();

        return back()->with('success', 'تم حفظ ملاحظات المتابعة على التوصيل بنجاح.');
    }

    public function updateDesignFollowup(Request $request, Order $order)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 🛡️ فقط أدمن أو ديزاينر
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            abort(403, 'غير مصرح لك بتعديل ملاحظات المتابعة على التصميم.');
        }

        // ✅ فاليديشين بسيط
        $data = $request->validate([
            'design_followup_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $text = trim($data['design_followup_note'] ?? '');

        // لو الفورم فاضي → اعتبرها بدون تغيير
        if ($text === '') {
            return back()->with('success', 'تم حفظ ملاحظات المتابعة (لم يتم إضافة ملاحظة جديدة).');
        }

        // ✅ إنشاء نوت جديدة في جدول notes مربوطة بالطلب
        $note = $order->notes()->create([
            'content' => $text,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'تم حفظ ملاحظات المتابعة على التصميم بنجاح.');
    }
}
