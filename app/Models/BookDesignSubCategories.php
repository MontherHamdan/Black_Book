<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookDesignSubCategories extends Model
{
    use HasFactory;

    protected $table = 'book_design_sub_categories';

    protected $fillable = ['name', 'category_id'];

    public function bookDesignCategory()
    {
        return $this->hasMany(BookDesignCategories::class);
    }

    public function BookDesign()
    {
        return $this->belongsTo(BookDesign::class);
    }
}
