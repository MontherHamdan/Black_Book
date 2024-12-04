<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
