<?php

/** @var PHPFramework\Application $app */

use App\Controllers\IndexController;
use App\Controllers\PostController;
use App\Controllers\UserController;
use PHPFramework\Middlewares\AuthMiddleware;
use PHPFramework\Middlewares\GuestMiddleware;
use PHPFramework\Middlewares\TestMiddleware;

$app->router()->get('/', [IndexController::class, 'index']);

$app->router()->get('/register', [UserController::class, 'register'])->addMiddleware(GuestMiddleware::class);
$app->router()->post('/register', [UserController::class, 'register'])->addMiddleware(GuestMiddleware::class);
$app->router()->get('/login', [UserController::class, 'login'])->addMiddleware(GuestMiddleware::class);
$app->router()->post('/login', [UserController::class, 'auth'])->addMiddleware(GuestMiddleware::class);
$app->router()->get('/logout', [UserController::class, 'logout'])->addMiddleware(AuthMiddleware::class);

router()->group('/posts', [
    router()->get('/create', [PostController::class, 'create']),
    router()->post('/create', [PostController::class, 'store']),

    router()->get('/edit', [PostController::class, 'edit']),
    router()->put('/update', [PostController::class, 'update']),
    router()->delete('/delete', [PostController::class, 'delete']),
    router()->get('/(?P<slug>[a-z0-9-]+)', [PostController::class, 'show']),
])->middleware(AuthMiddleware::class);
