<?php

use App\Models\Recipe;
use App\Models\User;
use Laravel\Passport\Passport;

it('filters by name and cuisine', function () {
    $user = User::factory()->create(['role' => 'user']);
    Passport::actingAs($user);

    Recipe::factory()->create(['user_id' => $user->id, 'name' => 'Chicken Biryani', 'cuisine_type' => 'South Asian']);
    Recipe::factory()->create(['user_id' => $user->id, 'name' => 'Pasta', 'cuisine_type' => 'Italian']);

    $firstRes = $this->getJson('/api/v1/recipes?name=Chicken');
    $firstRes->assertOk()->assertJsonFragment(['name' => 'Chicken Biryani'])
        ->assertJsonMissing(['name' => 'Pasta']);

    $secondRes = $this->getJson('/api/v1/recipes?cuisine_type=Italian');
    $secondRes->assertOk()->assertJsonFragment(['cuisine_type' => 'Italian']);
});
