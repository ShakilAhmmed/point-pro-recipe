<?php

namespace App\Actions\V1;

use App\Models\Recipe;
use Illuminate\Http\UploadedFile;

class CreateRecipeAction
{
    public function execute(array $fields, int $userId, ?UploadedFile $file = null): Recipe
    {
        $path = $file?->store("recipes/{$userId}", ['disk' => 'public']);

        $recipe = Recipe::query()->create([
            'user_id' => $userId,
            'name' => $fields['name'],
            'cuisine_type' => $fields['cuisine_type'],
            'image' => $path,
        ]);

        $recipe->ingredients()->createMany(
            array_map(fn ($i) => [
                'name' => $i['name'],
                'quantity' => $i['quantity'] ?? null,
                'unit' => $i['unit'] ?? null,
            ], $fields['ingredients'])
        );

        $recipe->steps()->createMany(
            array_map(fn ($s) => [
                'step_no' => (int) $s['step_no'],
                'description' => $s['description'],
            ], $fields['steps'])
        );

        return $recipe->load(['ingredients', 'steps']);

    }
}
