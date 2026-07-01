<?php
// DELETE AFTER USE

$logFile = __DIR__ . '/storage/logs/laravel.log';

echo "<pre style='font-size:12px;background:#1e1e1e;color:#d4d4d4;padding:20px;word-wrap:break-word;'>";
echo "<b style='color:#4ec9b0'>=== LARAVEL LOG (last 100 lines) ===</b>\n\n";

if (file_exists($logFile)) {
    $lines = file($logFile);
    $last  = array_slice($lines, -100);
    echo htmlspecialchars(implode('', $last));
} else {
    echo "Log file NOT found at: " . $logFile . "\n";
    echo "Checking storage folder:\n";
    $storageDir = __DIR__ . '/storage/logs/';
    if (is_dir($storageDir)) {
        echo "storage/logs/ EXISTS\n";
        $files = scandir($storageDir);
        echo "Files: " . implode(', ', $files) . "\n";
    } else {
        echo "storage/logs/ does NOT exist\n";
    }
}

echo "\n\n<b style='color:#4ec9b0'>=== SERVER INFO ===</b>\n";
echo "PHP: "    . PHP_VERSION . "\n";
echo "Dir: "    . __DIR__ . "\n";
echo "Storage writable: "       . (is_writable(__DIR__.'/storage') ? 'YES' : 'NO') . "\n";
echo "Bootstrap/cache writable: " . (is_writable(__DIR__.'/bootstrap/cache') ? 'YES' : 'NO') . "\n";

echo "\n<b style='color:red'>DELETE THIS FILE NOW!</b></pre>";
