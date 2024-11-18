<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookDesignResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => $this->image,
            'category_en' => $this->category->name,
            'category_ar' => $this->category->arabic_name,
            'subcategory_en' => optional($this->subcategory)->name,
            'subcategory_ar' => optional($this->subcategory)->arabic_name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
