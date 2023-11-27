<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class PushController extends Controller
{
    public function index(Request $request, Response $response, $args)
    {
        $this->c->pusher->broadcast('chat', [
            'payload' => 'abc'
        ]);

        $this->c->pusher->broadcastPrivate('order.5', [
            'payload' => 'abc'
        ]);

        $this->c->pusher->broadcastPrivate('user.1', [
            'payload' => 'abc'
        ]);
    }
}
