<?php

use App\Models\Order;
use App\Models\User;

$broadcast = $container->broadcast;

$broadcast->privateChannel('order.*', function (User $user, $orderId) {
    $order = Order::find($orderId);

    if ($order && $order->count()) {
        return $order->first()->belongsToUser($user);
    }

    return false;
});

$broadcast->privateChannel('user.*', function (User $user, $userId) {
    return $user->id === (int) $userId;
});

