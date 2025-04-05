<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'active' => (bool)$this->active,
            'parent_id' => $this->parent_id,
            'parent' => $this->parent ? new CategoryResource($this->parent) : null,
            'created_at' => (new \DateTime($this->created_at))->format('m-d-Y'),
            'updated_at' => (new \DateTime($this->updated_at))->format('m-d-Y'),
        ];
    }
}
