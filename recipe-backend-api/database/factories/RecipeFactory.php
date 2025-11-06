<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => ucfirst($this->faker->words(3, true)),
            'cuisine_type' => $this->faker->randomElement([
                'Italian', 'Indian', 'Chinese', 'Mexican', 'South Asian', 'American',
            ]),
            'image' => $this->faker->imageUrl(640, 480, 'food', true),
        ];
    }
}
