<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SvgCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function svgs()
    {
        return $this->hasMany(Svg::class, 'category_id');
    }
}
