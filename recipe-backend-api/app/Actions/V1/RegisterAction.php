<?php

namespace App\Actions\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RegisterAction
{
    public function execute(array $data): Model|User
    {
        return User::query()->create($data);
    }
}
