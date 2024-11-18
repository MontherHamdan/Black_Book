<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BookType extends Model
{
    protected $table = "book_types";

    protected $fillable = ['image', 'price', 'description_en', 'description_ar'];


    use HasFactory;

    public function subMedia()
    {
        return $this->hasMany(BookTypeSubMedia::class);
    }
}
