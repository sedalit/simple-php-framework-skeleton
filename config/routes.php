<?php

/** @var PHPFramework\Application $app */

use App\Controllers\IndexController;

$app->router()->get('/', [IndexController::class, 'index']);