<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Path: public_html/index.php
// Project folder: one level up at ~/gobazzar-app/
// Adjust '__DIR__/../' paths to point to your project folder

if (file_exists($maintenance = __DIR__.'/../gobazzar-app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../gobazzar-app/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../gobazzar-app/bootstrap/app.php';

$app->handleRequest(Request::capture());
