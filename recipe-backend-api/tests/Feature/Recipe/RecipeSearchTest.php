<?php

use App\Models\Recipe;
use App\Models\User;
use Laravel\Passport\Passport;

it('filters by name and cuisine', function () {
    $user = User::factory()->create(['role' => 'user']);
    Passport::actingAs($user);

    Recipe::factory()->create(['user_id' => $user->id, 'name' => 'Chicken Biryani', 'cuisine_type' => 'South Asian', 'visibility' => 1]);
    Recipe::factory()->create(['user_id' => $user->id, 'name' => 'Pasta', 'cuisine_type' => 'Italian', 'visibility' => 1]);

    $firstRes = $this->getJson('/api/v1/recipes?name=Chicken');
    $firstRes->assertOk()->assertJsonMissing(['name' => 'Pasta']);

    $secondRes = $this->getJson('/api/v1/recipes?cuisine_type=Italian');
    $secondRes->assertOk()->assertJsonFragment(['cuisine_type' => 'Italian']);
});
