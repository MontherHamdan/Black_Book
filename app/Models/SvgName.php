<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SvgName extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'normalized_name',
        'svg_code',
    ];

    
}
