<?php
if (($_GET['key'] ?? '') !== 'gobazzar-deploy-2026') { http_response_code(403); die('Forbidden'); }

$base = dirname(__DIR__);
echo "<pre style='font-family:monospace;font-size:13px;background:#111;color:#0f0;padding:20px'>";

function run($cmd, $base) {
    echo "$ $cmd\n";
    $out = shell_exec("cd " . escapeshellarg($base) . " && $cmd 2>&1");
    echo ($out ?: '(no output)') . "\n";
    return $out;
}

// 1. Git status
echo "=== GIT STATUS ===\n";
run('git status', $base);

// 2. Git remote
echo "=== GIT REMOTE ===\n";
run('git remote -v', $base);

// 3. Git log local vs remote
echo "=== LOCAL vs REMOTE ===\n";
run('git log --oneline -3', $base);
echo "\n";
run('git fetch origin 2>&1', $base);
run('git log --oneline origin/main -3', $base);

// 4. Force pull
echo "\n=== FORCE PULL ===\n";
run('git reset --hard origin/main', $base);
run('git pull origin main', $base);

// 5. Verify new file exists
echo "\n=== VERIFY FILES ===\n";
$files = [
    'app/Http/Controllers/AssistantController.php',
    'public/sw.js',
    'public/manifest.json',
    'resources/views/push-subscribe.blade.php',
];
foreach ($files as $f) {
    $path = $base . '/' . $f;
    echo (file_exists($path) ? '✅' : '❌') . " $f\n";
}

// 6. Clear caches
echo "\n=== CACHE CLEAR ===\n";
run('php artisan config:clear', $base);
run('php artisan route:clear', $base);
run('php artisan view:clear', $base);
run('php artisan route:cache', $base);
run('php artisan view:cache', $base);

echo "\n=== DONE ===\n";
echo "</pre>";
