<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\V1\ResetPasswordAction;
use App\Actions\V1\SendPasswordResetLinkAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgotPasswordFormRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class PasswordManageController extends Controller
{
    /**
     * @param ForgotPasswordFormRequest $request
     * @param SendPasswordResetLinkAction $action
     * @return JsonResponse
     */
    public function sendResetLink(ForgotPasswordFormRequest $request, SendPasswordResetLinkAction $action): JsonResponse
    {
        try {
            $status = $action->execute($request->input('email'));
            if ($status === Password::RESET_LINK_SENT) {
                logger()->info("authentication:reset-link -> for {$request->input('email')}");
                return $this->successResponse(message: __($status), code: Response::HTTP_OK);
            }
            return $this->successResponse(message: "If the email is registered, a reset link has been sent.", code: Response::HTTP_OK);
        } catch (Exception $exception) {
            logger()->critical("authentication:reset-link -> {$exception->getMessage()}");
            return $this->errorResponse(message: $exception->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function reset(ResetPasswordRequest $request, ResetPasswordAction $action): JsonResponse
    {
        try {
            $status = $action->execute(
                email: $request->input('email'),
                password: $request->input('password'),
                token: $request->input('token'),
            );

            if ($status === Password::PASSWORD_RESET) {
                logger()->info("authentication:reset -> for {$request->input('email')}");
                return $this->successResponse(message: "Password has been reset and sessions revoked.", code: Response::HTTP_OK);
            }
            return $this->errorResponse(message: __($status), code: Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $exception) {
            logger()->critical("authentication:reset -> {$exception->getMessage()}");
            return $this->errorResponse(message: $exception->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
