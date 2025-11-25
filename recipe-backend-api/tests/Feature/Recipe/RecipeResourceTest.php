<?php

use App\Models\Recipe;
use App\Models\User;
use Laravel\Passport\Passport;

it('user creates and lists own recipes; cannot view others recipe', function () {
    $me = User::factory()->create(['role' => 'user']);
    $other = User::factory()->create(['role' => 'user']);
    $otherRecipe = Recipe::factory()->create(['user_id' => $other->id]);

    Passport::actingAs($me);

    $create = $this->postJson('/api/v1/recipes', [
        'name' => 'My Curry',
        'cuisine_type' => 'Indian',
        'visibility' => 0,
        'ingredients' => [
            ['name' => 'Curry Powder', 'quantity' => '2', 'unit' => 'tbsp'],
        ],
        'steps' => [
            ['step_no' => 1, 'description' => 'Mix.'],
        ],
    ]);

    $create->assertCreated();

    $list = $this->getJson('/api/v1/recipes');
    $list->assertOk()
        ->assertJsonFragment(['name' => 'My Curry']);

    $show = $this->getJson("/api/v1/recipes/{$otherRecipe->id}");
    $show->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
});
