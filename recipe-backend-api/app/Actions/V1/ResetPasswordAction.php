<?php

namespace App\Actions\V1;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordAction
{
    public function execute(string $email, string $password, string $token): string
    {
        return Password::reset(
            ['email' => $email, 'password' => $password, 'password_confirmation' => $password, 'token' => $token],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                // Revoke ALL access tokens after a password reset (Passport)
                foreach ($user->tokens as $accessToken) {
                    $accessToken->revoke();
                    DB::table('oauth_refresh_tokens')
                        ->where('access_token_id', $accessToken->id)
                        ->update(['revoked' => true]);
                }
            }
        );
    }
}
