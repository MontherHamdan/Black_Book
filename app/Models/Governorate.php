<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;
    protected $fillable = ['name_en', 'name_ar', 'logestechs_id'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
