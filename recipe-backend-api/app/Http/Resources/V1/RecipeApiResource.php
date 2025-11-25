<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RecipeApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cuisine_type' => $this->cuisine_type,
            'image' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'ingredients' => IngredientApiResource::collection($this->whenLoaded('ingredients')),
            'steps' => StepApiResource::collection($this->whenLoaded('steps')),
            'visibility' => $this->visibility,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
