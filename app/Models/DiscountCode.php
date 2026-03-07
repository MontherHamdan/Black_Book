<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = ['discount_code', 'discount_value', 'discount_type', 'code_name'];

    public function tiers()
    {
        return $this->hasMany(DiscountCodeTier::class)->orderBy('min_qty');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
