<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'governorate_id'];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
