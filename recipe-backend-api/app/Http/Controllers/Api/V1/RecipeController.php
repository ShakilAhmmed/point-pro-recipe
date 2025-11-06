<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\V1\CreateRecipeAction;
use App\Actions\V1\FetchRecipeAction;
use App\Actions\V1\UpdateRecipeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RecipeFormRequest;
use App\Http\Resources\V1\RecipeApiResource;
use App\Models\Recipe;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RecipeController extends Controller
{
    public function index(Request $request, FetchRecipeAction $fetchRecipeAction): JsonResponse
    {
        try {
            $recipes = $fetchRecipeAction->execute($request);
            logger()->info("recipes:fetch -> {$request->user()->id}");

            return $this->successResponse(data: RecipeApiResource::collection($recipes),
                message: 'Recipes fetched successfully.',
                code: Response::HTTP_OK);
        } catch (Exception $exception) {
            logger()->critical('recipes:fetch -> '.$exception->getMessage());

            return $this->errorResponse('Recipes fetch failed.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(RecipeFormRequest $request, CreateRecipeAction $createRecipeAction): JsonResponse
    {
        try {
            DB::beginTransaction();
            $recipe = $createRecipeAction->execute($request->fields(), $request->user()->id, $request->file('image'));
            DB::commit();
            logger()->info("recipes:store -> {$recipe->id}");

            return $this->successResponse(
                data: new RecipeApiResource($recipe),
                message: 'Recipe created successfully.',
                code: Response::HTTP_CREATED
            );
        } catch (Exception|Throwable $e) {
            logger()->critical('recipes:store -> '.$e->getMessage());

            return $this->errorResponse('Server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Recipe $recipe): JsonResponse
    {
        try {
            Gate::authorize('view', $recipe);
            logger()->info("recipes:show -> {$recipe->id}");

            return $this->successResponse(
                data: new RecipeApiResource($recipe->load(['ingredients', 'steps'])),
                message: 'Recipe fetched successfully.',
                code: Response::HTTP_OK
            );
        } catch (AuthorizationException $exception) {
            logger()->critical('recipes:show -> '.$exception->getMessage());

            return $this->errorResponse('Unauthorized.', Response::HTTP_UNAUTHORIZED);
        } catch (Exception|Throwable $exception) {
            logger()->critical('recipes:show -> '.$exception->getMessage());

            return $this->errorResponse('Server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(RecipeFormRequest $request, Recipe $recipe, UpdateRecipeAction $updateRecipeAction): JsonResponse
    {
        try {
            Gate::authorize('update', $recipe);
            DB::beginTransaction();
            $recipe = $updateRecipeAction->execute(
                recipe: $recipe,
                fields: $request->fields(),
                file: $request->file('image'));
            DB::commit();
            logger()->info("recipes:update -> {$recipe->id}");

            return $this->successResponse(
                data: new RecipeApiResource($recipe),
                message: 'Recipe updated successfully.',
                code: Response::HTTP_CREATED
            );
        } catch (AuthorizationException $exception) {
            logger()->critical('recipes:update -> '.$exception->getMessage());

            return $this->errorResponse('Unauthorized.', Response::HTTP_UNAUTHORIZED);
        } catch (Exception|Throwable $e) {
            logger()->critical('recipes:update -> '.$e->getMessage());

            return $this->errorResponse('Server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Recipe $recipe): JsonResponse
    {
        try {
            Gate::authorize('delete', $recipe);
            DB::beginTransaction();
            logger()->info("recipes:delete -> {$recipe->id}");
            $recipe->delete();
            DB::commit();

            return $this->successResponse(
                message: 'Recipe deleted successfully.',
                code: Response::HTTP_OK
            );
        } catch (AuthorizationException $exception) {
            logger()->critical('recipes:delete -> '.$exception->getMessage());

            return $this->errorResponse('Unauthorized.', Response::HTTP_UNAUTHORIZED);
        } catch (Exception|Throwable $e) {
            logger()->critical('recipes:delete -> '.$e->getMessage());

            return $this->errorResponse('Server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
