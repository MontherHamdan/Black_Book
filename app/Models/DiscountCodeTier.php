<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCodeTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_code_id',
        'min_qty',
        'discount_value',
        'discount_type',
    ];

    protected $casts = [
        'min_qty' => 'integer',
        'discount_value' => 'decimal:2',
    ];

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }
}
