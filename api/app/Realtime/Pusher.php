<?php

namespace App\Realtime;

use React\EventLoop\LoopInterface;
use React\ZMQ\SocketWrapper;
use Thruway\Peer\Client;

class Pusher extends Client
{
    protected $socket;

    public function __construct($realm, LoopInterface $loop, SocketWrapper $socket)
    {
        parent::__construct($realm, $loop);
        $this->socket = $socket;
    }

    public function onSessionStart($session, $transport)
    {
        $this->socket->on('message', [$this, 'transmit']);
    }

    public function transmit($payload)
    {
        $payload = json_decode($payload, false);

        if (!$this->containsRequiredPayloadProperties($payload)) {
            return;
        }

        $this->getSession()->publish(
            $this->getUniqueChannel($payload),
            [$payload->payload]
        );
    }

    protected function getUniqueChannel($payload)
    {
        return "{$payload->app_id}_{$payload->channel}";
    }

    protected function containsRequiredPayloadProperties($payload)
    {
        return property_exists($payload, 'channel') && property_exists($payload, 'app_id');
    }
}
