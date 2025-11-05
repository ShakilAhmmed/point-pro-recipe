<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\V1\RegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterFormRequest;
use App\Http\Resources\V1\UserApiResource;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller
{
    public function store(RegisterFormRequest $request, RegisterAction $registerAction)
    {
        try {
            $user = $registerAction->execute($request->fields());
            logger()->info('authentication:registration ->' . $user->id);
            return $this->successResponse(data: new UserApiResource($user), message: "User Registration Successful.", code: Response::HTTP_CREATED);
        } catch (Exception $exception) {
            logger()->critical('authentication:registration ->' . $exception->getMessage());
            return $this->errorResponse(message: $exception->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
