<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiplomaMajor extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = ['diploma_id', 'name'];

    // Define the inverse of the relationship (a major belongs to a diploma)
    public function diploma()
    {
        return $this->belongsTo(Diploma::class);
    }
}
