<?php

namespace App\Realtime;

use GuzzleHttp\Client;

class Pusher
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function broadcast($channel, array $payload)
    {
        $this->client->request('POST', 'push', [
            'json' => [
                'channel' => $channel,
                'payload' => $payload
            ]
        ]);
    }

    public function broadcastPrivate($channel, array $payload)
    {
        $this->broadcast($channel . '.private', $payload);
    }

    public function authorize($channel, $sessionId)
    {
        $this->client->request('POST', 'push/auth', [
            'json' => [
                'channel' => $channel,
                'session_id' => $sessionId,
            ]
        ]);
    }
}
