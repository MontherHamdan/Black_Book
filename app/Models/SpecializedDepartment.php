<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecializedDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'phone_number',
        'whatsapp_link',
        'icon_svg',
        'color_code',
    ];
}
