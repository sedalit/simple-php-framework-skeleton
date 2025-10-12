<?php

$startFrameworkTime = microtime(true);

if (PHP_MAJOR_VERSION < 8) {
    die("Require PHP version >= 8");
}

require_once __DIR__ . '/../config/init.php';
require_once ROOT . '/vendor/autoload.php';
require_once CONFIG . '/serviceProviders.php';

use PHPFramework\Application;
use PHPFramework\Services\CoreService;
use PHPFramework\Services\Mail\MailService;

$app = new Application($_SERVER['REQUEST_URI'], [
    CoreService::class,
    MailService::class,
    ...PROVIDERS
]);

require_once CONFIG . '/routes.php';

$app->run();

if (DEBUG) {
    dump("Time: " . microtime(true) - $startFrameworkTime);
}