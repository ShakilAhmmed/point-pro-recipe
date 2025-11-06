<?php

use App\Models\Recipe;
use App\Models\User;

it('allows owner to view recipe', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

    expect($owner->can('view', $recipe))->toBeTrue();
});

it('denies non-owner non-admin to view recipe', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $owner->id]);
    $anotherUser = User::factory()->create(['role' => 'user']);

    expect($anotherUser->can('view', $recipe))->toBeFalse();
});

it('allows admin to view any recipe', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

    expect($admin->can('view', $recipe))->toBeTrue();
});

it('allows owner to update recipe', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

    expect($owner->can('update', $recipe))->toBeTrue();
});

it('denies non-owner to update recipe', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $owner->id]);
    $anotherUser = User::factory()->create(['role' => 'user']);

    expect($anotherUser->can('update', $recipe))->toBeFalse();
});

it('allows admin to update recipe', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $recipe = Recipe::factory()->create();

    expect($admin->can('update', $recipe))->toBeTrue();
});

it('allows delete for owner and admin, denies non-owner', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);
    $anotherUser = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

    expect($owner->can('delete', $recipe))->toBeTrue()
        ->and($admin->can('delete', $recipe))->toBeTrue()
        ->and($anotherUser->can('delete', $recipe))->toBeFalse();
});
