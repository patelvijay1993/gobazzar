<?php
$secret = $_GET['key'] ?? '';
if ($secret !== 'gobazzar-deploy-2026') { die('Unauthorized'); }

echo '<pre style="background:#111;color:#0f0;padding:20px;font-family:monospace">';
echo "=== Mail Test ===\n\n";

// 1. Check if port 465 is reachable
echo "--- Port Check ---\n";
$ports = [
    'smtp.gmail.com:465' => ['smtp.gmail.com', 465],
    'smtp.gmail.com:587' => ['smtp.gmail.com', 587],
    'smtp.gmail.com:25'  => ['smtp.gmail.com', 25],
];
foreach ($ports as $label => [$host, $port]) {
    $conn = @fsockopen($host, $port, $errno, $errstr, 5);
    if ($conn) {
        echo "$label: OPEN ✓\n";
        fclose($conn);
    } else {
        echo "$label: BLOCKED ✗ ($errstr)\n";
    }
}

// 2. Try sending via PHPMailer-style raw SMTP
echo "\n--- Env Check ---\n";
$env = parse_ini_file(dirname(__DIR__) . '/.env');
echo "MAIL_HOST: " . ($env['MAIL_HOST'] ?? 'NOT SET') . "\n";
echo "MAIL_PORT: " . ($env['MAIL_PORT'] ?? 'NOT SET') . "\n";
echo "MAIL_SCHEME: " . ($env['MAIL_SCHEME'] ?? 'NOT SET') . "\n";
echo "MAIL_USERNAME: " . ($env['MAIL_USERNAME'] ?? 'NOT SET') . "\n";
echo "MAIL_FROM_ADDRESS: " . ($env['MAIL_FROM_ADDRESS'] ?? 'NOT SET') . "\n";

// 3. Try Laravel mail via artisan
echo "\n--- Laravel Mail Test ---\n";
$output = shell_exec('cd ' . dirname(__DIR__) . ' && php artisan tinker --execute="
try {
    Mail::raw(\'Test from live server\', function(\$m) {
        \$m->to(\'vijaypateldeveloper@gmail.com\')->subject(\'GoBazaar Live Test\');
    });
    echo \'SUCCESS\';
} catch(Exception \$e) {
    echo \'ERROR: \' . \$e->getMessage();
}
" 2>&1');
echo $output . "\n";

echo "\n=== DONE — DELETE THIS FILE ===";
echo '</pre>';
