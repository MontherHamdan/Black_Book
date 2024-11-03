<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookDesign extends Model
{
    use HasFactory;

    protected $table = 'book_designs';
    protected $fillable = ['image', 'category_id', 'sub_category_id'];

    public function category()
    {
        return $this->belongsTo(BookDesignCategory::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(BookDesignSubCategory::class, 'sub_category_id');
    }
}
