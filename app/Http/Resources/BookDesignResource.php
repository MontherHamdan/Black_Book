<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookDesignResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => $this->image ?? 'N/A', // Full image URL
            'category_en' => $this->category->name ?? 'N/A',
            'category_ar' => $this->category->arabic_name ?? 'غير متوفر',
            'categoryType' => $this->category->type,
            'subcategory_en' => $this->subcategory->name ?? 'N/A',
            'subcategory_ar' => $this->subcategory->arabic_name ?? 'غير متوفر',
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
