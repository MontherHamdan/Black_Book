<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BookTypeColor extends Model
{
    use HasFactory;

    protected $table = 'book_type_colors';

    protected $fillable = [
        'book_type_id',
        'name_ar',
        'name_en',
        'color_code',
        'image',
        'order_display',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('book_types');
        });

        static::deleted(function () {
            Cache::forget('book_types');
        });
    }

    public function bookType()
    {
        return $this->belongsTo(BookType::class);
    }
}
