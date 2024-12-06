<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_type_id',
        'book_design_id',
        'front_image_id',
        'back_image_ids',
        'user_type',
        'arabic_name',
        'english_name',
        'university_id',
        'major_id',
        'svg_id',
        'svg_title',
        'note',
        'user_phone_number',
        'pages_number',
        'is_sponge',
        'is_option',
        'add_photo_option_id',
        'transparent_print_option_id',
        'gift_option',
        'first_delivery_phone_number',
        'second_delivery_phone_number',
        'total_price',
        'payment_method',
    ];

    protected $casts = [
        'back_image_ids' => 'array',
        'is_sponge' => 'boolean',
        'is_option' => 'boolean',
    ];

    // Relationships
    public function bookType()
    {
        return $this->belongsTo(BookType::class);
    }

    public function bookDesign()
    {
        return $this->belongsTo(BookDesign::class);
    }

    public function frontImage()
    {
        return $this->belongsTo(UserImage::class, 'front_image_id');
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function backImages()
    {
        return $this->hasMany(UserImage::class, 'id', 'back_image_ids');
    }
}
