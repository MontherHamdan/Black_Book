<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'discount_price',
        'book_price',
        'features',
        'person_number',
    ];

    protected $casts = [
        'features' => 'array',
        'discount_price' => 'decimal:2',
        'book_price' => 'decimal:2',
    ];
}