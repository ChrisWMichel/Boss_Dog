<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
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
            'status' => $this->status,
            'total' => $this->total,
            'user' => new UserResource($this->user),
            //'customer' => new UserResource($this->user->customer),
            'number_of_items' => $this->items->count(),
            'created_at' => (new \DateTime($this->created_at))->format('m-d-Y'),
            'updated_at' => (new \DateTime($this->updated_at))->format('m-d-Y'),
            
        ];
    }
}
