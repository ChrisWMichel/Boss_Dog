<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'status' => $this->status,
            'email' => $this->user->email,
            'shipping_address' => $this->whenLoaded('shippingAddress', function() {
                return [
                    'address1' => $this->shippingAddress->address1,
                    'address2' => $this->shippingAddress->address2,
                    'city' => $this->shippingAddress->city,
                    'state' => $this->shippingAddress->state,
                    'zip_code' => $this->shippingAddress->zip_code,
                    'country_code' => $this->shippingAddress->country_code,
                ];
            }),
            'billing_address' => $this->whenLoaded('billingAddress', function() {
                return [
                    'address1' => $this->billingAddress->address1,
                    'address2' => $this->billingAddress->address2,
                    'city' => $this->billingAddress->city,
                    'state' => $this->billingAddress->state,
                    'zip_code' => $this->billingAddress->zip_code,
                    'country_code' => $this->billingAddress->country_code,
                ];
            }),
            'updated_at' => $this->formatted_updated_at,
            'created_at' => $this->formatted_created_at,
        ];
    }
}
