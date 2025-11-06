<?php

namespace App\Actions\V1;

use App\Models\Recipe;
use Illuminate\Http\Request;

class FetchRecipeAction
{
    public function execute(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $name = $request->query('name');
        $cuisine = $request->query('cuisine_type');
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        return Recipe::query()
            ->with('user', 'ingredients', 'steps')
            ->withCount(['ingredients', 'steps'])
            ->when(! $isAdmin, fn ($query) => $query->where('user_id', $user->id))
            ->when($name, fn ($query) => $query->where('name', 'like', "%{$name}%"))
            ->when($cuisine, fn ($query) => $query->where('cuisine_type', 'like', "%{$cuisine}%"))
            ->orderByDesc('created_at')
            ->paginate(3);
    }
}
