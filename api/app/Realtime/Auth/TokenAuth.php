<?php

namespace App\Realtime\Auth;

use App\Models\Token;
use Thruway\Authentication\AbstractAuthProviderClient;

class TokenAuth extends AbstractAuthProviderClient
{
    public function getMethodName()
    {
        return 'token';
    }

    public function processAuthenticate($signature)
    {
        $token = Token::byKey($signature)->first();

        if ($token) {
            return ['SUCCESS'];
        }

        return ['FAILURE'];
    }
}
