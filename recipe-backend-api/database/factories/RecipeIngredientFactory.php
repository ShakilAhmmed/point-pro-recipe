<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecipeIngredient>
 */
class RecipeIngredientFactory extends Factory
{
    protected $model = RecipeIngredient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::factory(),
            'name' => ucfirst($this->faker->word()),
            'quantity' => $this->faker->numberBetween(1, 500),
            'unit' => $this->faker->randomElement(['g', 'kg', 'ml', 'pcs', 'tbsp', 'tsp']),
        ];
    }
}
