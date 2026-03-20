<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'title' => $this->faker->words(3, true),
        'image_path' => $this->faker->imageUrl(1200, 400, 'business'),
        'link' => $this->faker->url(),
        'is_active' => true,
    ];
}
}
