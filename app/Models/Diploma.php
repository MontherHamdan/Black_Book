<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diploma extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = ['name', 'governorate_name', 'governorate_id', 'city_id', 'area_id'];

    // Define the one-to-many relationship (a diploma can have many majors)
    public function majors()
    {
        return $this->hasMany(DiplomaMajor::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
