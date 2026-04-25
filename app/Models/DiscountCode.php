<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = ['discount_code', 'discount_value', 'discount_type', 'code_name', 'is_group', 'plan_id'];

    public function tiers()
    {
        return $this->hasMany(DiscountCodeTier::class)->orderBy('min_qty');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
