<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diploma extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = ['name', 'governorate_name'];

    // Define the one-to-many relationship (a diploma can have many majors)
    public function majors()
    {
        return $this->hasMany(DiplomaMajor::class);
    }
}
