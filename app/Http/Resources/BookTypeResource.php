<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookTypeResource extends JsonResource
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
            'image' => $this->image,
            'price' => $this->price,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'sub_media' => BookTypeSubMediaResource::collection($this->subMedia),
        ];
    }
}
