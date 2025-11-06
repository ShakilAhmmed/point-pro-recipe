<?php

use App\Models\Recipe;
use App\Models\User;
use Laravel\Passport\Passport;

it('admin can view update delete any recipe', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $owner->id, 'name' => 'Temp']);

    Passport::actingAs($admin);

    $this->getJson("/api/v1/recipes/{$recipe->id}")->assertOk();

    $this->putJson("/api/v1/recipes/{$recipe->id}", [
        'name' => 'Updated Name',
        'cuisine_type' => 'Fusion',
        'ingredients' => [
            ['name' => 'X', 'quantity' => '1', 'unit' => 'pcs'],
        ],
        'steps' => [
            ['step_no' => 1, 'description' => 'Do'],
        ],
    ])->assertCreated();

    $this->deleteJson("/api/v1/recipes/{$recipe->id}")->assertOk();
});
