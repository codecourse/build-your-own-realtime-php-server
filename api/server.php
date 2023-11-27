<?php

use App\Realtime\Auth\TokenAuth;
use App\Realtime\Pusher;
use React\ZMQ\Context;
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;

require __DIR__ . '/bootstrap/app.php';

$router = new Router();
$loop = $router->getLoop();

$context = new Context($loop);
$pull = $context->getSocket(\ZMQ::SOCKET_PULL);
$pull->bind($container->settings['zmq']['host']);

$router->registerModules([
    new \Thruway\Authentication\AuthenticationManager(),
    new App\Realtime\Auth\Subscription()
]);

$router->addInternalClient(new TokenAuth(['default']));

$router->addTransportProvider(new RatchetTransportProvider('0.0.0.0', 7474));
$router->addInternalClient(new Pusher('default', $loop, $pull));

$router->start();
