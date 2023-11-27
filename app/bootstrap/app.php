<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => getenv('APP_DEBUG') === 'true',

        'app' => [
            'name' => getenv('APP_NAME')
        ],

        'views' => [
            'cache' => getenv('VIEW_CACHE_DISABLED') === 'true' ? false : __DIR__ . '/../storage/views'
        ],

        'pusher' => [
            'secret' => getenv('PUSHER_SECRET'),
            'endpoint' => getenv('PUSHER_ENDPOINT'),
        ],

        'db' => [
            'driver' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]
    ],
]);

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['pusher'] = function ($container) {
    $client = new GuzzleHttp\Client([
        'headers' => [
            'Authorization' => $container->settings['pusher']['secret']
        ],
        'base_uri' => $container->settings['pusher']['endpoint']
    ]);

    return new App\Realtime\Pusher($client);
};

$container['broadcast'] = function ($container) {
    return new App\Realtime\Broadcast($container->user);
};

$container['user'] = function ($container) {
    $user = App\Models\User::find(1);

    $_SESSION['user_id'] = $user->id;

    return $user;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => $container->settings['views']['cache']
    ]);

    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    $view->getEnvironment()->addGlobal('user', $container->user);

    return $view;
};

require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/broadcast.php';
