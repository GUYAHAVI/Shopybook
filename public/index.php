<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Temporary diagnostic: log incoming host and request to storage/logs/host_access.log
// Remove this after debugging
try {
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'unknown');
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $line = date('Y-m-d H:i:s') . "\t$ip\t$host\t$uri\n";
    @file_put_contents(__DIR__ . '/../storage/logs/host_access.log', $line, FILE_APPEND | LOCK_EX);
} catch (\Throwable $e) {
    // ignore logging errors during diagnosis
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
