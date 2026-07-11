<?php
// Shared-hosting deploy script — git pull + artisan
// URL: https://gobazzarweb.heavendwell.com/deploy.php?key=gobazzar-deploy-2026
// DELETE after use if you want extra security, or keep for re-use.

$secret = $_GET['key'] ?? '';
if ($secret !== 'gobazzar-deploy-2026') {
    http_response_code(403);
    die('Unauthorized');
}

echo '<pre style="font-family:monospace;font-size:13px;background:#111;color:#0f0;padding:20px;margin:0;min-height:100vh">';
echo "=== GoBazaar Deploy ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

$base = dirname(__DIR__); // project root (one level up from public/)

function run($cmd) {
    global $base;
    echo "$ $cmd\n";
    $out = shell_exec("cd " . escapeshellarg($base) . " && $cmd 2>&1");
    echo ($out ?: '(no output)') . "\n";
}

// 1. Git pull
echo "--- Git Pull ---\n";
run('git pull origin main');

// 2. Composer (skip if vendor/ already exists and no composer.json change needed)
// Uncomment if you change composer.json:
// run('composer install --no-dev --optimize-autoloader --no-interaction');

// 3. Storage permissions
echo "--- Storage Permissions ---\n";
$dirs = [
    'storage', 'storage/app', 'storage/app/public',
    'storage/framework', 'storage/framework/cache',
    'storage/framework/sessions', 'storage/framework/views',
    'storage/logs', 'bootstrap/cache'
];
foreach ($dirs as $dir) {
    $path = $base . '/' . $dir;
    if (!is_dir($path)) mkdir($path, 0775, true);
    chmod($path, 0775);
    echo "ok: $dir\n";
}

// 4. Artisan commands
echo "\n--- Artisan ---\n";
run('php artisan config:clear');
run('php artisan cache:clear');
run('php artisan view:clear');
run('php artisan route:clear');
run('php artisan migrate --force');
run('php artisan storage:link');
run('php artisan config:cache');
run('php artisan route:cache');
run('php artisan view:cache');

// 5. Status check
echo "\n--- Status ---\n";
echo "APP_URL: " . (getenv('APP_URL') ?: '(not set)') . "\n";
echo "Storage writable: " . (is_writable($base.'/storage') ? 'YES ✓' : 'NO ✗') . "\n";
echo "Bootstrap/cache writable: " . (is_writable($base.'/bootstrap/cache') ? 'YES ✓' : 'NO ✗') . "\n";

// Show last 5 git commits to confirm pull worked
echo "\n--- Last 5 Commits ---\n";
run('git log --oneline -5');

echo "\n=== DONE ===\n";
echo '</pre>';
