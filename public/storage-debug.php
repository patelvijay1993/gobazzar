<?php
// Upload this to public_html/gobazzarweb.heavendwell.com/storage-debug.php
// Visit: https://gobazzarweb.heavendwell.com/storage-debug.php
// DELETE after debugging

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../gobazzar-app/vendor/autoload.php';
$app = require_once __DIR__ . '/../gobazzar-app/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$disk = \Illuminate\Support\Facades\Storage::disk('public');
$config = config('filesystems.disks.public');

echo '<pre>';
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n\n";
echo "Storage disk root: " . $config['root'] . "\n";
echo "Storage disk url:  " . $config['url'] . "\n\n";
echo "Root exists: " . (is_dir($config['root']) ? 'YES' : 'NO') . "\n";
echo "Root writable: " . (is_writable($config['root']) ? 'YES' : 'NO') . "\n";
echo '</pre>';
