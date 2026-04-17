<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserImage;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "orders";

    // Status constants
    public const STATUS_NEW_ORDER = 'new_order'; // طلب جديد
    public const STATUS_NEEDS_MODIFICATION = 'needs_modification'; // يوجد تعديل
    public const STATUS_PENDING = 'Pending';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_SHIPPING = 'shipping'; // ضفتها لأنها بالكونترولر
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_RECEIVED = 'Received';
    public const STATUS_CANCELED = 'Canceled';

    protected $fillable = [
        'user_gender',
        'discount_code_id',
        'book_type_id',
        'book_design_id',
        'custom_design_image_id',
        'front_image_id',
        'back_image_ids',
        'user_type',
        'university_id',
        'university_major_id',
        'diploma_id',
        'diploma_major_id',
        'username_ar',
        'username_en',
        'svg_id',
        'svg_title',
        'note',
        'user_phone_number',
        'is_sponge',
        'pages_number',
        'additional_image_id',
        'transparent_printing_id',
        'delivery_number_one',
        'delivery_number_two',
        'address',

        'logestechs_order_id',
        'area_id',
        'city_id',
        'governorate_id',

        'delivery_followup_note',
        'design_followup_note',
        'binding_followup_note',
        'notebook_followup_note',

        'final_price',
        'final_price_with_discount',
        'status',
        'designer_done',
        'designer_done_at',
        'designer_commission',
        'paid_commission',
        'is_commission_paid',
        'commission_paid_at',
        'designer_id',
        'book_decorations_id',
        'gift_type',
        'gift_title',
        'designer_read_notes',
        'is_with_additives',
        'deleted_at',

        'designer_design_file',
        'designer_decoration_file',
        'designer_internal_files',
        'designer_gift_file',
        'is_design_downloaded',
        'is_internal_downloaded',
        'is_decoration_downloaded',
        'is_gift_downloaded',
        'dispatched_at',
    ];

    protected $casts = [
        'back_image_ids' => 'array',
        'additional_image_id' => 'array',
        'transparent_printing_ids' => 'array',
        'designer_done' => 'boolean',
        'designer_done_at' => 'datetime',
        'is_with_additives' => 'boolean',
        'gift_type' => 'string',
        'custom_design_image_id' => 'array',
        'designer_commission' => 'decimal:2',
        'designer_read_notes' => 'boolean',
        'is_commission_paid' => 'boolean',
        'commission_paid_at' => 'datetime',
        'designer_internal_files' => 'array',
        'is_design_downloaded' => 'boolean',
        'is_internal_downloaded' => 'boolean',
        'is_decoration_downloaded' => 'boolean',
        'is_gift_downloaded' => 'boolean',
        'dispatched_at' => 'datetime',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relations
     |--------------------------------------------------------------------------
     */
    protected static function booted()
    {
        static::saving(function (Order $order) {
            $order->recalculateAdditivesFlag();
        });
    }

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
        return $this->belongsTo(BookDecoration::class, 'book_decorations_id');
    }

    public function frontImage()
    {
        return $this->belongsTo(UserImage::class, 'front_image_id');
    }

    // public function additionalImage()
    // {
    //     return $this->belongsTo(UserImage::class, 'additional_image_id');
    // }

    public function transparentPrinting()
    {
        return $this->belongsTo(UserImage::class, 'transparent_printing_id');
    }

    public function svg()
    {
        return $this->belongsTo(Svg::class, 'svg_id');
    }


    public function getBackImagesAttribute()
    {
        $backImageIds = $this->back_image_ids;

        // لو كانت مخزنة كنص JSON
        if (is_string($backImageIds)) {
            $backImageIds = json_decode($backImageIds, true);
        }

        if (!is_array($backImageIds) || empty($backImageIds)) {
            return collect();
        }

        return UserImage::whereIn('id', $backImageIds)->get();
    }


    public function notes()
    {
        return $this->hasMany(Note::class);
    }


    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }


    // public function customDesignImage()
    // {
    //     return $this->belongsTo(UserImage::class, 'custom_design_image_id');
    // }


    public function additionalImagesFromIds()
    {
        $ids = $this->additional_image_id;

        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $ids = $decoded;
            }
        }

        if (!is_array($ids) || empty($ids)) {
            return collect();
        }

        return UserImage::whereIn('id', $ids)->get();
    }


    public function getTransparentPrintingIdsAttribute($value)
    {
        if (!$value && !is_null($this->transparent_printing_id)) {
            return [$this->transparent_printing_id];
        }

        return json_decode($value, true) ?? [];
    }



    public function calculateIsWithAdditives(): bool
    {
        $hasSponge = (bool) $this->is_sponge;

        $back = $this->back_image_ids;
        if (is_string($back)) {
            $decoded = json_decode($back, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $back = $decoded;
            }
        }
        $hasBackImgs = is_array($back) && !empty($back);

        $additional = $this->additional_image_id;
        if (is_string($additional)) {
            $decoded = json_decode($additional, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $additional = $decoded;
            }
        }
        $hasAdditionalImages = is_array($additional) && !empty($additional);

        $hasTransparent = !is_null($this->transparent_printing_id);

        return $hasSponge || $hasBackImgs || $hasAdditionalImages || $hasTransparent;
    }


    public function recalculateAdditivesFlag(): void
    {
        $this->is_with_additives = $this->calculateIsWithAdditives();
    }
    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
    }

    public function universityMajor()
    {
        return $this->belongsTo(Major::class, 'university_major_id');
    }

    public function diploma()
    {
        return $this->belongsTo(Diploma::class, 'diploma_id');
    }

    public function diplomaMajor()
    {
        return $this->belongsTo(DiplomaMajor::class, 'diploma_major_id');
    }

    /*
     |--------------------------------------------------------------------------
     | Accessors للعرض في الـ Blade: school_name / major_name
     |--------------------------------------------------------------------------
     */

    public function getSchoolNameAttribute()
    {
        if ($this->user_type === 'university') {
            return $this->university->name ?? null;
        }

        if ($this->user_type === 'diploma') {
            return $this->diploma->name ?? null;
        }

        return null;
    }

    public function getMajorNameAttribute()
    {
        if ($this->user_type === 'university') {
            return $this->universityMajor->name ?? null;
        }

        if ($this->user_type === 'diploma') {
            return $this->diplomaMajor->name ?? null;
        }

        return null;
    }

    public function customDesignImagesFromIds()
    {
        $ids = $this->custom_design_image_id;

        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $ids = $decoded;
            }
        }

        if (!is_array($ids) || empty($ids)) {
            return collect();
        }

        return UserImage::whereIn('id', $ids)->get();
    }

    public function governorate() { return $this->belongsTo(Governorate::class); }
    public function city() { return $this->belongsTo(City::class); }
    public function area() { return $this->belongsTo(Area::class); }
}
