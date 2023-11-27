<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class PushAuthController extends Controller
{
    public function store(Request $request, Response $response, $args)
    {
        $broadcast = $this->c->broadcast;

        if (!$broadcast->checkAuthorization($request->getParam('channel'))) {
            return $response->withStatus(401);
        }

        $this->c->pusher->authorize(
            $request->getParam('channel'), $request->getParam('session_id')
        );
    }
}
