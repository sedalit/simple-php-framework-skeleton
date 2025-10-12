<?php

const DB_CHARSET = 'utf8mb4';

define('DB', [
    'host' => env('DB_HOST'),
    'dbname' => env('DB_NAME'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => env('DB_CHARSET', DB_CHARSET),
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ],
]);

const TABLES_WHITELIST = [
    'users', 'posts'
];