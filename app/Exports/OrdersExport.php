<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\Exportable;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithCustomCsvSettings
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $q = Order::query()
            ->with(['bookType', 'notes']); // تأكد تضيف notes هنا لو بدك

        // فلتر الحالة
        if (!empty($this->filters['status'])) {
            $q->where('status', $this->filters['status']);
        }

        // ✅ فلتر الإضافات (ملاحظات)
        if (!empty($this->filters['additives'])) {
            if ($this->filters['additives'] === 'with_additives') {
                // الطلبات اللي عليها ملاحظات
                $q->whereHas('notes');
            } elseif ($this->filters['additives'] === 'with_out_additives') {
                // الطلبات اللي ما عليها ولا ملاحظة
                $q->whereDoesntHave('notes');
            }
        }

        // فلتر التاريخ
        if (!empty($this->filters['date_from'])) {
            $q->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $q->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        // فلتر البحث
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];

            $q->where(function ($q) use ($search) {
                $q->where('username_ar', 'like', "%$search%")
                    ->orWhere('username_en', 'like', "%$search%")
                    ->orWhere('governorate', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('user_phone_number', 'like', "%$search%")
                    ->orWhere('delivery_number_two', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('final_price_with_discount', 'like', "%$search%")
                    ->orWhere('school_name', 'like', "%$search%");
            });
        }

        return $q->orderBy('id', 'desc');
    }

    public function headings(): array
    {
        return [
            'رقم الطلب',
            'التاريخ',
            'اسم المستخدم',
            'نوع الطلب',
            'المحافظة',
            'العنوان',
            'الجامعة',
            'رقم الهاتف',
            'رقم الهاتف 2',
            'الحالة',
            'السعر',
        ];
    }

    public function map($order): array
    {
        $createdAt = Carbon::parse($order->created_at)
            ->timezone('Asia/Amman')
            ->format('d-m-Y, h:i A');

        return [
            $order->id,
            $createdAt,
            $order->username_ar . ' / ' . $order->username_en,
            optional($order->bookType)->name_ar,
            $order->governorate,
            $order->address,
            $order->school_name,
            $order->user_phone_number,
            $order->delivery_number_two,
            $order->status,
            $order->final_price_with_discount,
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            // خليك على الكوما لو حاب
            'delimiter'        => ',',
            // أهم شيء: BOM + UTF-8 عشان Excel
            'use_bom'          => true,
            'output_encoding'  => 'UTF-8',
        ];
    }
}
