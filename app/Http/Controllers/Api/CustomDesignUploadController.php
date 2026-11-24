<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomDesignUpload;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomDesignUploadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'purpose' => 'required|in:decoration,gift',
            'upload_token' => 'required|uuid',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|mimetypes:application/pdf,image/jpeg,image/png,image/webp|max:20480',
        ]);
        $file = $request->file('file');
        $path = $file->store('custom_designs/'.$data['purpose'], 'local');
        abort_unless($path, 500, 'تعذّر حفظ الملف، يرجى المحاولة مرة أخرى.');

        try {
            $upload = CustomDesignUpload::create([
                'purpose' => $data['purpose'],
                'token_hash' => hash('sha256', $data['upload_token']),
                'path' => $path,
                'original_name' => mb_substr(preg_replace('/[\x00-\x1F\x7F]/u', '', basename(str_replace('\\', '/', $file->getClientOriginalName()))), 0, 200),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return response()->json(['data' => $upload->metadata()], 201);
    }

    public function preview(Request $request, CustomDesignUpload $upload)
    {
        return $this->fileResponse($upload, $request->boolean('download'));
    }

    public function destroy(Request $request, CustomDesignUpload $upload)
    {
        $data = $request->validate(['upload_token' => 'required|uuid']);
        DB::transaction(function () use ($upload, $data) {
            $locked = CustomDesignUpload::whereKey($upload->id)->lockForUpdate()->firstOrFail();
            abort_unless(hash_equals($locked->token_hash, hash('sha256', $data['upload_token'])), 403);
            abort_if($locked->claimed_at !== null, 409, 'هذا التصميم مرتبط بطلب ولا يمكن حذفه.');
            $locked->delete();
        });
        return response()->noContent();
    }

    public function orderFile(Request $request, Order $order, string $purpose)
    {
        abort_unless($request->user()->canAccessAdmin(), 403);
        abort_unless(in_array($purpose, ['decoration', 'gift'], true), 404);
        $upload = $purpose === 'decoration' ? $order->customDecorationUpload : $order->customGiftUpload;
        abort_unless($upload, 404);
        return $this->fileResponse($upload, ! $request->boolean('preview'));
    }

    private function fileResponse(CustomDesignUpload $upload, bool $download)
    {
        $disk = Storage::disk('local');
        abort_unless($disk->exists($upload->path), 404);
        return $disk->response($upload->path, $upload->original_name, [
            'Content-Type' => $upload->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ], $download ? 'attachment' : 'inline');
    }
}
