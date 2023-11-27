<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Authorization;
use ZMQ;
use ZMQContext;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class PushController extends Controller
{
    public function store(Request $request, Response $response, $args)
    {
        $context = new ZMQContext();

        $socket = $context->getSocket(ZMQ::SOCKET_PUSH);
        $socket->connect($this->c->settings['zmq']['host']);

        $socket->send(
            $this->mergeAppIdIntoBody(
                $request->getParsedBody(),
                $request->getAttribute('app_id')
            )
        );
    }

    public function authorize(Request $request, Response $response, $args)
    {
        $body = (object) $request->getParsedBody();

        Authorization::create([
            'session_id' => $body->session_id,
            'channel' => $request->getAttribute('app_id') . '_' . $body->channel,
        ]);
    }

    protected function mergeAppIdIntoBody($body, $appId)
    {
        return json_encode(array_merge($body, ['app_id' => $appId]));
    }
}
