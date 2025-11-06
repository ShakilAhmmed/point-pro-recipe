<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Token;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginFormRequest;
use App\Http\Resources\V1\UserApiResource;
use App\Services\V1\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticationController extends Controller
{
    public function login(LoginFormRequest $request): JsonResponse
    {
        try {
            return AuthService::userWithToken($request->fields());
        } catch (Throwable $exception) {
            logger()->critical("authentication:login -> {$exception->getMessage()}");

            return $this->errorResponse(message: $exception->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function logout(): JsonResponse
    {
        auth()->user()->token()->revoke();

        return $this->successResponse(data: [], message: 'Logged out successfully');
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $user = auth()->user();
        try {
            logger()->info("authentication:refreshToken -> for {$user->email}");

            return response()->json([
                'code' => Response::HTTP_OK,
                'data' => new UserApiResource($user),
                'token' => [
                    'access_token' => AuthService::refreshToken($user),
                    'token_type' => 'bearer',
                    'expires_in_days' => Token::TOKEN_TTL_DAYS->value,
                ],
            ]);
        } catch (Throwable $exception) {
            logger()->critical("authentication:refreshToken for {$user->email}->{$exception->getMessage()}");

            return $this->errorResponse(message: $exception->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
