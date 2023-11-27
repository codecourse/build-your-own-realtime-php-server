<?php

namespace App\Realtime;

use App\Models\User;

class Broadcast
{
    protected $user;

    protected $channels = [];

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function channel($pattern, callable $callback)
    {
        $this->channels[$pattern] = $callback;
    }

    public function privateChannel($pattern, callable $callback)
    {
        $this->channel($pattern . '.private', $callback);
    }

    public function getChannels()
    {
        return $this->channels;
    }

    public function checkAuthorization($channel)
    {
        $channelParts = explode('.', $channel);

        foreach ($this->getChannels() as $pattern => $callback) {
            if ($this->foundChannelMatchingPattern($channel, $pattern)) {
                break;
            }
        }

        return call_user_func_array(
            $callback, array_merge([$this->user], $this->extractParamsForChannel($channel, $pattern))
        );
    }

    protected function extractParamsForChannel($channel, $pattern)
    {
        $params = [];
        $channelParts = explode('.', $channel);

        foreach (explode('.', $pattern) as $index => $patternPart) {
            if ($patternPart !== '*') {
                continue;
            }

            $params[] = $channelParts[$index];
        }

        return $params;
    }

    protected function foundChannelMatchingPattern($channel, $pattern)
    {
        $channelParts = explode('.', $channel);

        foreach ($patternParts = explode('.', $pattern) as $index => $part) {
            if ($part !== '*') {
                continue;
            }

            $patternParts[$index] = $channelParts[$index] ?? '*';
        }

        return implode('.', $channelParts) === implode('.', $patternParts);
    }
}
