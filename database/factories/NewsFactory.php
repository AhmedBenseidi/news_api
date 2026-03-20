<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'user_id' => \App\Models\User::all()->random()->id, // اختيار مستخدم موجود
        'category_id' => \App\Models\Category::all()->random()->id, // اختيار قسم موجود
        'title' => $this->faker->sentence(6),
        'content' => $this->faker->paragraphs(4, true),
        'image_url' => $this->faker->imageUrl(800, 600, 'news'), // صورة عشوائية
        'views' => $this->faker->numberBetween(0, 5000),
    ];
}
}
