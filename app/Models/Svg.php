<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Svg extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'svg_code', 'category_id'];

    public function category()
    {
        return $this->belongsTo(SvgCategory::class, 'category_id');
    }
}
