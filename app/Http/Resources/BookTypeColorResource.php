<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookTypeColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'book_type_id' => $this->book_type_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'color_code' => $this->color_code,
            'image' => $this->image,
            'order_display' => $this->order_display,
        ];
    }
}
