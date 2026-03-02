<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "book_types";

    protected $fillable = ['image', 'name_en', 'name_ar', 'price', 'description_en', 'description_ar', 'deleted_at'];

    public function subMedia()
    {
        return $this->hasMany(BookTypeSubMedia::class);
    }
}
