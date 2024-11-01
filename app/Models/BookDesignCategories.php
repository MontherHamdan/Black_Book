<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookDesignCategories extends Model
{
    use HasFactory;

    protected $table = 'book_design_categories';

    protected $fillable = ['name'];

    public function bookDesignSubCategory()
    {
        return $this->hasMany(BookDesignSubCategories::class);
    }
}
