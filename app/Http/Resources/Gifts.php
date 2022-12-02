<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Gifts extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'images' => json_decode($this->images),
            'price' => $this->price,
            'countWishlist' => $this->wishlist ?? 0,
            'countReviews' => $this->reviews ?? 0,
            'countRating' => $this->rating,
            'isNew' => $this->new_gift
        ];
    }
}
