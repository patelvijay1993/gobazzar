<?php
// SECURITY: Delete this file immediately after use!
$secret = $_GET['key'] ?? '';
if ($secret !== 'gobazzar-deploy-2026') { die('Unauthorized'); }

// Load Laravel env
$env = parse_ini_file(dirname(__DIR__) . '/.env');

$pdo = new PDO(
    "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8",
    $env['DB_USERNAME'],
    $env['DB_PASSWORD']
);

echo '<pre style="background:#111;color:#0f0;padding:20px;font-family:monospace">';
echo "=== Admin Fix ===\n\n";

// 1. Verify all admin emails
$stmt = $pdo->prepare("UPDATE users SET email_verified_at = NOW() WHERE email_verified_at IS NULL");
$stmt->execute();
echo "Verified " . $stmt->rowCount() . " unverified users\n";

// 2. Show admin user status
$stmt = $pdo->query("SELECT id, name, email, email_verified_at, is_admin FROM users WHERE email = 'admin@gobazzar.com'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    echo "\nAdmin user:\n";
    print_r($user);
} else {
    echo "\nAdmin user NOT found — checking all users:\n";
    $stmt = $pdo->query("SELECT id, name, email, is_admin FROM users LIMIT 10");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
}

echo "\n=== DONE — DELETE THIS FILE NOW! ===";
echo '</pre>';
