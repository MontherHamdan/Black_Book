<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAdditionalImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'image',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function userImage()
    {
        return $this->belongsTo(UserImage::class, 'image'); 
    }
}
