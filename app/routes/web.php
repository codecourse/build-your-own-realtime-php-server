<?php

use App\Controllers\HomeController;
use App\Controllers\PushAuthController;
use App\Controllers\PushController;

$app->get('/', HomeController::class . ':index');
$app->get('/push', PushController::class . ':index');

$app->post('/push/auth', PushAuthController::class . ':store');
