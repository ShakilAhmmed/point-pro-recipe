<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        $admins = User::factory()->count(3)->create(['role' => 'admin']);
        $users = User::factory()->count(17)->create(['role' => 'user']);
        $pool = $admins->concat($users);

        $recipes = Recipe::factory()
            ->count(500)
            ->state(fn () => ['user_id' => $pool->random()->id])
            ->create();

        $recipes->each(function (Recipe $recipe) {
            // Ingredients
            $ingredientCount = fake()->numberBetween(3, 6);
            RecipeIngredient::factory()
                ->count($ingredientCount)
                ->create(['recipe_id' => $recipe->id]);

            // Steps
            $stepCount = fake()->numberBetween(3, 6);
            RecipeStep::factory()
                ->count($stepCount)
                ->sequence(fn ($sequence) => ['step_no' => $sequence->index + 1])
                ->create(['recipe_id' => $recipe->id]);
        });
        DB::commit();
    }
}
