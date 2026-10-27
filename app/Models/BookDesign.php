<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookDesign extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'book_designs';

    protected $fillable = [
        'design_name',
        'order_display',
        'image',
        'category_id',
        'sub_category_id',
        'is_uploaded_by_user',
        'is_image_required',
    ];

    protected $casts = [
        'order_display' => 'integer',
        'is_uploaded_by_user' => 'boolean',
        'is_image_required' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(BookDesignCategory::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(BookDesignSubCategory::class, 'sub_category_id');
    }
}
