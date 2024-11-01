<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookTypeSubMedia extends Model
{
    use HasFactory;

    protected $table = 'book_type_sub_media';

    protected $fillable = ['media','type'];

    public function bookType()
    {
        return $this->belongsTo(BookType::class);
    }
}
