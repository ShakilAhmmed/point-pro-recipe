<?php

namespace App\Services\V1;

use App\Enums\Authentication;
use App\Enums\Token;
use App\Http\Resources\V1\UserApiResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    public static function userWithToken(array $fields): JsonResponse
    {
        $user = User::query()->where('email', $fields['email'])->first();
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            logger()->warning("authentication:login -> failed for {$fields['email']}");
            return response()->json([
                'status' => 'Error',
                'message' => "Invalid Credentials.",
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => null,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        }
        $token = $user->createToken(Authentication::TOKEN->value)->accessToken;
        logger()->info("authentication:login -> success for {$fields['email']}");
        return response()
            ->json([
                'code' => Response::HTTP_OK,
                'data' => new UserApiResource($user),
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in_days' => Token::TOKEN_TTL_DAYS->value
                ],
            ]);
    }

    /**
     * @param User $user
     * @return string
     */
    public static function refreshToken(User $user): string
    {
        $current = $user->token();
        if ($current) {
            $current->revoke();
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $current->id)
                ->update(['revoked' => true]);
        }
        return $user->createToken(Authentication::TOKEN->value)->accessToken;
    }
}
