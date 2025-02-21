<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = "orders";

    // Status constants
    public const STATUS_PENDING         = 'Pending';
    public const STATUS_PREPARING       = 'preparing';
    public const STATUS_COMPLETED        = 'Completed';
    public const STATUS_OUT_FOR_DELIVERY = 'Out for Delivery';
    public const STATUS_RECEIVED        = 'Received';
    public const STATUS_CANCELED        = 'Canceled';

    protected $fillable = [
        'user_gender',
        'discount_code_id',
        'book_type_id',
        'book_design_id',
        'front_image_id',
        'book_decorations_id', 
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
        'deleted_at',
        'gift_title',
        'is_with_additives',
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

    public function bookDecoration()
    {
        return $this->belongsTo(BookDecoration::class,'book_decorations_id');
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

    public function svg()
    {
        return $this->belongsTo(Svg::class, 'svg_id');
    }

    public function backImages()
    {
        // Ensure back_image_ids is an array
        $backImageIds = $this->back_image_ids;

        if (is_string($backImageIds)) {
            $backImageIds = json_decode($backImageIds, true);
        }

        if (!is_array($backImageIds) || empty($backImageIds)) {
            return collect(); // Return an empty collection if not valid
        }

        return UserImage::whereIn('id', $backImageIds)->get();
    }

    public function handleBackImages()
    {
        // Assuming 'UserImage' is the related model and 'back_image_ids' stores the image IDs
        return $this->hasMany(UserImage::class, 'id', 'back_image_ids');
    }


}
