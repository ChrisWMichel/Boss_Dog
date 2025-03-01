<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text(30),
            'image' => 'https://resource.logitech.com/w_600,h_600,ar_1,c_pad,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/logitech/en/homepage/delorean-hp/hero-webcam-mx-anywhere3s-product-mobile.png',
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 20, 5000),
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => 1,
            'updated_by' => 1,
            'image_size' => 1024,
        ];
    }
}
