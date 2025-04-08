<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id'             => $this->id,
      'name'           => $this->name,
      'description'    => $this->description,
      'price'          => $this->price,
      'active_ingred'  => $this->active_ingred,
      'manufacture'    => $this->manufacture,
      'expiry_date'    => $this->expiry_date,
      'quantity'       => $this->quantity,
      'rating'         => $this->reviews->avg('rating'),
      'comments'       => $this->reviews->map(function ($rating) { 
        return [
          'user'    => $rating->user->name ?? 'Unknown',
          'rating'  => $rating->rating,
          'comment' => $rating->comment,
          'date'    => $rating->created_at->toDateString(),
        ];
      }),
    ];
  }
}
