<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookTypeSubMedia extends Model
{
    use HasFactory;

    protected $table = 'book_type_sub_media';

    protected $fillable = ['book_type_id', 'media', 'type'];

    public function bookType()
    {
        return $this->belongsTo(BookType::class);
    }
}
