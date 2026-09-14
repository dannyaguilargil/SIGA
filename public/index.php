<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// SiteGround deployment keeps the application in ../siga and this public
// directory in public_html. Local development keeps the standard layout.
$basePath = is_file(__DIR__.'/../siga/bootstrap/app.php')
    ? realpath(__DIR__.'/../siga')
    : dirname(__DIR__);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $basePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
