<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookDesign extends Model
{
    use HasFactory;

    protected $table = 'book_designs';
    protected $fillable = ['image', 'category_id', 'sub_category_id'];

    public function BookDesigncategory()
    {
        return $this->belongsTo(BookDesignCategories::class);
    }

    public function BookDesignsubCategory()
    {
        return $this->belongsTo(BookDesignSubCategories::class);
    }
}
