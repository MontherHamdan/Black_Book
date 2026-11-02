<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'page_options';

    protected $fillable = [
        'pages',
        'label',
        'extra_price',
        'is_default',
        'badge',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'pages' => 'integer',
        'extra_price' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope for querying active options in display order.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('pages', 'asc');
    }
}
