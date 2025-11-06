<?php

use App\Actions\V1\CreateRecipeAction;
use App\Actions\V1\UpdateRecipeAction;
use App\Models\User;
use Illuminate\Http\UploadedFile;

it('updates recipe and replaces ingredients and steps', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $createPayload = [
        'name' => 'Pasta',
        'cuisine_type' => 'Italian',
        'ingredients' => [
            ['name' => 'Pasta', 'quantity' => '200', 'unit' => 'g'],
        ],
        'steps' => [
            ['step_no' => 1, 'description' => 'Boil.'],
        ],
    ];

    $recipe = app(CreateRecipeAction::class)->execute($createPayload, $user->id, UploadedFile::fake()->image('pasta.jpg'));

    $updatePayload = [
        'name' => 'Pasta (Creamy)',
        'cuisine_type' => 'Italian',
        'ingredients' => [
            ['name' => 'Pasta', 'quantity' => '250', 'unit' => 'g'],
            ['name' => 'Cream', 'quantity' => '100', 'unit' => 'ml'],
        ],
        'steps' => [
            ['step_no' => 1, 'description' => 'Boil pasta.'],
            ['step_no' => 2, 'description' => 'Add cream.'],
        ],
    ];

    $updated = app(UpdateRecipeAction::class)->execute($recipe, $updatePayload, UploadedFile::fake()->image('pasta2.jpg'));

    expect($updated->name)->toBe('Pasta (Creamy)')
        ->and($updated->ingredients)->toHaveCount(2)
        ->and($updated->steps)->toHaveCount(2);
});
