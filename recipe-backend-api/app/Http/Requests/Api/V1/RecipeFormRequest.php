<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class RecipeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cuisine_type' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.quantity' => ['required', 'integer', 'max:50'],
            'ingredients.*.unit' => ['required', 'string', 'max:50'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.step_no' => ['required', 'integer', 'min:1'],
            'steps.*.description' => ['required', 'string'],
        ];
    }

    public function fields(): array
    {
        return [
            'name' => $this->input('name'),
            'cuisine_type' => $this->input('cuisine_type'),
            'image' => $this->input('image'),
            'ingredients' => $this->input('ingredients'),
            'steps' => $this->input('steps'),
        ];
    }
}
