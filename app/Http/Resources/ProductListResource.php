<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
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
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'image' => $this->image,
            'quantity' => $this->quantity,
            'published' => (bool)$this->published,
            'updated_at' => (new \DateTime($this->updated_at))->format('m-d-Y'), 
        ];
    }
}
