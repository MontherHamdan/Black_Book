<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class CustomDesignUpload extends Model
{
    use HasUuids;

    protected $fillable = ['purpose', 'token_hash', 'path', 'original_name', 'mime_type', 'size', 'claimed_at'];
    protected $hidden = ['token_hash', 'path'];
    protected $casts = ['claimed_at' => 'datetime', 'size' => 'integer'];

    public function metadata(): array
    {
        return [
            'id' => $this->id,
            'purpose' => $this->purpose,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'preview_url' => URL::signedRoute('api.custom-designs.preview', ['upload' => $this->id]),
            'download_url' => URL::signedRoute('api.custom-designs.preview', ['upload' => $this->id, 'download' => 1]),
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (self $upload) {
            Storage::disk('local')->delete($upload->path);
        });
    }
}
