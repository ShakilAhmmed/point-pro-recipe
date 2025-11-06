<?php

use App\Actions\V1\CreateRecipeAction;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\UploadedFile;

it('creates recipe with ingredients and steps', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $picture = UploadedFile::fake()->image('dish.jpg');

    $payload = [
        'name' => 'Chicken Biryani',
        'cuisine_type' => 'South Asian',
        'ingredients' => [
            ['name' => 'Rice', 'quantity' => '500', 'unit' => 'g'],
            ['name' => 'Chicken', 'quantity' => '1', 'unit' => 'kg'],
        ],
        'steps' => [
            ['step_no' => 1, 'description' => 'Marinate.'],
            ['step_no' => 2, 'description' => 'Cook.'],
        ],
    ];

    $recipe = app(CreateRecipeAction::class)->execute($payload, $user->id, $picture);

    expect($recipe)->toBeInstanceOf(Recipe::class)
        ->and($recipe->ingredients)->toHaveCount(2)
        ->and($recipe->steps)->toHaveCount(2)
        ->and($recipe->image)->not()->toBeNull();
    Storage::disk('public')->assertExists($recipe->image);
});
