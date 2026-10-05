<?php

/**
 * Router for PHP's built-in server.
 *
 * Laravel's own `artisan serve` router resolves the public directory from
 * getcwd(), which breaks when the project path contains a space (e.g.
 * "C:\Users\Dana Hussin\..."). This router resolves from __DIR__ instead, so it
 * works regardless of where the project lives.
 *
 *   composer serve
 */

$publicPath = dirname(__DIR__) . '/public';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');

// Let the built-in server serve real files (assets, images) directly.
if ($uri !== '/' && file_exists($publicPath . $uri)) {
    return false;
}

require_once $publicPath . '/index.php';
