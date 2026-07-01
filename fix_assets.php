<?php
// SECURITY: Delete this file immediately after running!
chdir(__DIR__);
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";

$kernel->call('filament:assets');
echo $kernel->output();

$kernel->call('config:clear');
$kernel->call('view:clear');
$kernel->call('cache:clear');
$kernel->call('route:clear');

echo "All done! DELETE THIS FILE NOW.</pre>";
