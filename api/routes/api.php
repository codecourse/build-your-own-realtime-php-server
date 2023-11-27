<?php

use App\Controllers\PushController;
use App\Middleware\Authenticate;

$app->add(new Authenticate());

$app->post('/push', PushController::class . ':store');
$app->post('/push/auth', PushController::class . ':authorize');