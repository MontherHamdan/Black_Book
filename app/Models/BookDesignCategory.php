<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookDesignCategory extends Model
{
    use HasFactory;

    protected $table = 'book_design_categories';

    protected $fillable = ['name', 'arabic_name','type'];

    public function subCategories()
    {
        return $this->hasMany(BookDesignSubCategory::class, 'category_id');
    }
}
