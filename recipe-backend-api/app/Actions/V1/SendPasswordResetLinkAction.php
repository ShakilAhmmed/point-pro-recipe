<?php

namespace App\Actions\V1;

use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkAction
{
    /**
     * @param string $email
     * @return string
     */
    public function execute(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }
}
