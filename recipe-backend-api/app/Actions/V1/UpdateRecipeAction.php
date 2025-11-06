<?php

namespace App\Actions\V1;

use App\Models\Recipe;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateRecipeAction
{
    public function execute(Recipe $recipe, array $fields, ?UploadedFile $file = null): Recipe
    {
        $imagePath = $recipe->image;

        if ($file) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $file->store("recipes/{$recipe->user_id}", ['disk' => 'public']);
        }

        $recipe->update([
            'name' => $fields['name'],
            'cuisine_type' => $fields['cuisine_type'],
            'image' => $imagePath,
        ]);

        $recipe->ingredients()->delete();
        $recipe->steps()->delete();
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
