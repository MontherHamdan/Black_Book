<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = "orders";


    protected $fillable = [
        'user_gender',
        'discount_code_id',
        'book_type_id',
        'book_design_id',
        'front_image_id',
        'back_image_ids',
        'user_type',
        'username_ar',
        'username_en',
        'school_name',
        'major_name',
        'svg_id',
        'svg_title',
        'note',
        'user_phone_number',
        'is_sponge',
        'pages_number',
        'book_accessory',
        'additional_image_id',
        'transparent_printing_id',
        'delivery_number_one',
        'delivery_number_two',
        'governorate',
        'address',
        'final_price',
        'final_price_with_discount',
        'status',
        'deleted_at'
    ];

    protected $casts = [
        'back_image_ids' => 'array', // Handle JSON arrays as PHP arrays
    ];

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

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

    public function additionalImage()
    {
        return $this->belongsTo(UserImage::class, 'additional_image_id');
    }

    public function transparentPrinting()
    {
        return $this->belongsTo(UserImage::class, 'transparent_printing_id');
    }
}
