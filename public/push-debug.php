<?php
if (($_GET['key'] ?? '') !== 'gobazzar-deploy-2026') { http_response_code(403); die('Forbidden'); }

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<pre style='font-family:monospace;font-size:13px;background:#111;color:#0f0;padding:20px'>";

// 1. Check VAPID keys
$pub  = config('services.vapid.public_key');
$priv = config('services.vapid.private_key');
echo "VAPID_PUBLIC_KEY:  " . ($pub  ? substr($pub,0,20)."..." : "❌ MISSING") . "\n";
echo "VAPID_PRIVATE_KEY: " . ($priv ? substr($priv,0,10)."..." : "❌ MISSING") . "\n\n";

// 2. Check push_subscriptions table
try {
    $count = \DB::table('push_subscriptions')->count();
    echo "push_subscriptions table: ✅ exists ($count subscriptions)\n";
    $subs = \DB::table('push_subscriptions')->get();
    foreach ($subs as $s) {
        echo "  User #{$s->user_id}: " . substr($s->endpoint, 0, 60) . "...\n";
    }
} catch (\Exception $e) {
    echo "push_subscriptions table: ❌ " . $e->getMessage() . "\n";
    echo "Run migration: /deploy.php?key=gobazzar-deploy-2026\n";
}

echo "\n";

// 3. Test send push to a specific user
$testUserId = $_GET['user'] ?? null;
if ($testUserId && $pub && $priv) {
    echo "Sending test push to user #$testUserId...\n";
    \App\Http\Controllers\PushController::sendToUser(
        (int)$testUserId,
        'Test — GoBazaar',
        'Push notifications are working!',
        '/chat'
    );
    echo "Done! Check your phone.\n";
} else {
    echo "To test push: add ?user=USER_ID to URL\n";
    echo "Example: /push-debug.php?key=gobazzar-deploy-2026&user=6\n";
}

echo "</pre>";
