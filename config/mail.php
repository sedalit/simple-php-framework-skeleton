<?php

define('MAIL', [
    'host' => env('MAIL_HOST'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'port' => env('MAIL_PORT', 465),
    'smtpAuth' => env('MAIL_SMTP_AUTH', true),
    'smtpSecure' => env('MAIL_SMTP_SECURE', 'tls'),
]);