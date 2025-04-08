<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    if ($this->resource == null) {
      return [
        'status' => 'error',
        'message' => 'Product not found',
      ];
    }
    return [
      'status' => 'success',
      'id' => $this->id,
      'name' => $this->name,
      'description' => $this->description,
      'price' => $this->price,
      'category_id' => $this->category_id,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      'quantity' => $this->quantity,
      'active_ingred'=> $this->active_ingred,
      'expiry_date' => $this->expiry_date,
    ];
  }
}
