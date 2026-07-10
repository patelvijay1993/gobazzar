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
        echo "  public_key: " . ($s->public_key ? substr($s->public_key,0,20)."..." : "❌ NULL") . "\n";
        echo "  auth_token: " . ($s->auth_token ? substr($s->auth_token,0,20)."..." : "❌ NULL") . "\n\n";
    }
} catch (\Exception $e) {
    echo "push_subscriptions table: ❌ " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Check /push/subscribe route directly
echo "--- Route Check ---\n";
$routes = app('router')->getRoutes();
$found = false;
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'push/subscribe')) {
        echo "Route: " . $route->methods()[0] . " /" . $route->uri() . "\n";
        echo "Middleware: " . implode(', ', $route->middleware()) . "\n";
        $found = true;
    }
}
if (!$found) echo "❌ /push/subscribe route NOT FOUND!\n";

echo "\n--- Users ---\n";
$users = \DB::table('users')->select('id','name','email')->get();
foreach ($users as $u) {
    echo "  #{$u->id}: {$u->name} ({$u->email})\n";
}

echo "\n";

// 4. Test send push to a specific user
$testUserId = $_GET['user'] ?? null;
if ($testUserId && $pub && $priv) {
    echo "Sending test push to user #$testUserId...\n";
    $subs = \DB::table('push_subscriptions')->where('user_id', $testUserId)->count();
    echo "Subscriptions for this user: $subs\n";
    if ($subs === 0) {
        echo "❌ No subscriptions found for user #$testUserId — cannot send push!\n";
        echo "User needs to visit /enable-notifications first.\n";
    } else {
        \App\Http\Controllers\PushController::sendToUser(
            (int)$testUserId,
            'Test — GoBazaar',
            'Push notifications are working!',
            '/chat'
        );
        echo "✅ Push sent! Check your phone.\n";
    }
} else {
    echo "To test push: /push-debug.php?key=gobazzar-deploy-2026&user=USER_ID\n";
}

echo "</pre>";
