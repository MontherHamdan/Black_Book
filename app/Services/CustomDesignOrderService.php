<?php

namespace App\Services;

use App\Models\CustomDesignUpload;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CustomDesignOrderService
{
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $tokenHash = hash('sha256', $data['design_upload_token'] ?? '');
            unset($data['design_upload_token']);
            $uploads = [];
            foreach (['custom_decoration_upload_id' => 'decoration', 'custom_gift_upload_id' => 'gift'] as $field => $purpose) {
                if (empty($data[$field])) {
                    continue;
                }
                $upload = CustomDesignUpload::whereKey($data[$field])->lockForUpdate()->first();
                if (! $upload || $upload->purpose !== $purpose || $upload->claimed_at !== null
                    || ! hash_equals($upload->token_hash, $tokenHash)
                    || ! Storage::disk('local')->exists($upload->path)) {
                    throw ValidationException::withMessages([$field => 'التصميم غير متاح لهذا الطلب، يرجى رفعه مجدداً.']);
                }
                $uploads[] = $upload;
            }

            $order = Order::create($data);
            foreach ($uploads as $upload) {
                $upload->update(['claimed_at' => now()]);
            }
            return $order;
        });
    }
}
