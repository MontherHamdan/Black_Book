<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookDesignSubCategory extends Model
{
    use HasFactory;

    protected $table = 'book_design_sub_categories';

    protected $fillable = ['name', 'arabic_name', 'category_id'];

    public function category()
    {
        return $this->belongsTo(BookDesignCategory::class, 'category_id');
    }

    public function designs()
    {
        return $this->hasMany(BookDesign::class, 'sub_category_id');
    }
}
