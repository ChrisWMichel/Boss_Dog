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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image, // Keep this for backward compatibility
            'images' => $this->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'path' => $image->path,
                    'mime' => $image->mime,
                    'size' => $image->size,
                    'position' => $image->position,
                ];
            }),
            'quantity' => $this->quantity,
            'published' => (bool)$this->published,
            'created_at' => (new \DateTime($this->created_at))->format('m-d-Y'),
            'updated_at' => (new \DateTime($this->updated_at))->format('m-d-Y'),
        ];
    }
}
