<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\V1\LoginTokenAction;
use App\Enums\Authentication;
use App\Enums\Token;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginFormRequest;
use App\Http\Resources\V1\UserApiResource;
use App\Models\User;
use App\Services\V1\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    /**
     * @param LoginFormRequest $request
     * @return JsonResponse
     */
    public function login(LoginFormRequest $request)
    {
        try {
            return AuthService::userWithToken($request->fields());
        } catch (Exception $exception) {
            logger()->critical('authentication:login ->' . $exception->getMessage());
            return $this->errorResponse(message: $exception->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->user()->token()->revoke();
        return $this->successResponse(data: [], message: 'Logged out successfully');
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function refreshToken(Request $request)
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
        } catch (Exception $exception) {
            logger()->critical("authentication:refreshToken for {$user->email}->{$exception->getMessage()}");
        }

    }
}
