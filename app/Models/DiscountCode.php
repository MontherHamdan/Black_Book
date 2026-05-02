<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_code', 'discount_value', 'discount_type', 'code_name', 'is_group', 'plan_id',
        'user_phone_number', 'delivery_number_two', 'university_id', 'governorate_id', 'city_id', 'area_id',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
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
