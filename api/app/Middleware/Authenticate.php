<?php

namespace App\Middleware;

use App\Models\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Authenticate
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        if (!$secret = $this->getTokenFromHeader($request)) {
            return $response->withStatus(401);
        }

        if (!$token = Token::bySecret($secret)->first()) {
            return $response->withStatus(401);
        }

        $request = $request->withAttribute('app_id', $token->app_id);

        return $next($request, $response);
    }

    protected function getTokenFromHeader(ServerRequestInterface $request)
    {
        if ($request->hasHeader('Authorization')) {
            return $request->getHeader('Authorization')[0];
        }
    }
}
