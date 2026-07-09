<?php
/**
 * GoBazaar — Setup classified category custom fields on server
 * Access: https://gobazzarweb.heavendwell.com/setup-fields.php?key=gobazzar-setup-2026
 * DELETE THIS FILE after use!
 */
define('KEY', 'gobazzar-setup-2026');
if (($_GET['key'] ?? '') !== KEY) {
    http_response_code(403);
    die('<h2 style="color:red">403 Forbidden — wrong key</h2>');
}

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo '<style>body{font-family:monospace;background:#1e1e1e;color:#d4d4d4;padding:20px}
.ok{color:#4ec9b0}.err{color:#f44747}.warn{color:#dcdcaa}h2{color:#569cd6}</style>';
echo '<h2>GoBazaar — Setup Classified Category Fields</h2>';

$action = $_GET['action'] ?? 'check';

// ── Check table ──────────────────────────────────────────────────────────────
echo '<h3 class="warn">1. Table Check</h3>';
$hasTable = Schema::hasTable('category_fields');
echo $hasTable
    ? '<p class="ok">✓ category_fields table EXISTS</p>'
    : '<p class="err">✗ category_fields table NOT FOUND — run migrate first!</p>';

if (!$hasTable) {
    echo '<p><a href="?key=' . KEY . '&action=migrate" style="color:#569cd6">→ Click here to run migrate</a></p>';
}

// ── Run migrate if requested ─────────────────────────────────────────────────
if ($action === 'migrate') {
    echo '<h3 class="warn">Running migrate...</h3><pre>';
    $output = [];
    exec('php ' . dirname(__DIR__) . '/artisan migrate --force 2>&1', $output);
    echo implode("\n", $output);
    echo '</pre>';
    $hasTable = Schema::hasTable('category_fields');
    echo $hasTable ? '<p class="ok">✓ Table created</p>' : '<p class="err">✗ Still missing</p>';
}

if (!$hasTable) { echo '</body>'; exit; }

// ── Show existing fields ─────────────────────────────────────────────────────
echo '<h3 class="warn">2. Existing Fields</h3>';
$existing = DB::table('category_fields')
    ->join('categories', 'categories.id', '=', 'category_fields.category_id')
    ->select('category_fields.*', 'categories.name as cat_name')
    ->orderBy('category_id')
    ->get();

echo '<p>Total fields: <b>' . $existing->count() . '</b></p>';
if ($existing->count() > 0) {
    echo '<table border="1" cellpadding="4" style="border-collapse:collapse;color:#d4d4d4">';
    echo '<tr style="background:#252526"><th>Cat ID</th><th>Category</th><th>Label</th><th>Key</th><th>Type</th></tr>';
    foreach ($existing as $f) {
        echo "<tr><td>{$f->category_id}</td><td>{$f->cat_name}</td><td>{$f->label}</td><td>{$f->key}</td><td>{$f->type}</td></tr>";
    }
    echo '</table>';
}

// ── Add classified fields ────────────────────────────────────────────────────
echo '<h3 class="warn">3. Add Classified Category Fields</h3>';

if ($action === 'add_fields') {
    $classifiedFields = [
        // Real Estate (id=1)
        1 => [
            ['label'=>'Property Type', 'key'=>'property_type', 'type'=>'select', 'options'=>json_encode(['House','Condo','Townhouse','Apartment','Commercial','Land']), 'placeholder'=>null, 'is_required'=>true, 'sort_order'=>1],
            ['label'=>'Bedrooms',      'key'=>'bedrooms',      'type'=>'select', 'options'=>json_encode(['Studio','1','2','3','4','5+']),                             'placeholder'=>null, 'is_required'=>false,'sort_order'=>2],
            ['label'=>'Bathrooms',     'key'=>'bathrooms',     'type'=>'select', 'options'=>json_encode(['1','1.5','2','2.5','3','3+']),                              'placeholder'=>null, 'is_required'=>false,'sort_order'=>3],
            ['label'=>'Square Feet',   'key'=>'sqft',          'type'=>'text',   'options'=>null, 'placeholder'=>'e.g. 1200',                                         'is_required'=>false,'sort_order'=>4],
        ],
        // Buy & Sell (id=2)
        2 => [
            ['label'=>'Condition',     'key'=>'condition',     'type'=>'select', 'options'=>json_encode(['New','Like New','Good','Fair','For Parts']),                'placeholder'=>null, 'is_required'=>false,'sort_order'=>1],
            ['label'=>'Brand',         'key'=>'brand',         'type'=>'text',   'options'=>null, 'placeholder'=>'e.g. Samsung',                                      'is_required'=>false,'sort_order'=>2],
        ],
        // Autos (id=5)
        5 => [
            ['label'=>'Vehicle Year',  'key'=>'vehicle_year',  'type'=>'text',   'options'=>null, 'placeholder'=>'e.g. 2020',                                         'is_required'=>false,'sort_order'=>1],
            ['label'=>'Make & Model',  'key'=>'make_model',    'type'=>'text',   'options'=>null, 'placeholder'=>'e.g. Toyota Camry',                                 'is_required'=>false,'sort_order'=>2],
            ['label'=>'Mileage (km)',  'key'=>'mileage',       'type'=>'text',   'options'=>null, 'placeholder'=>'e.g. 85000',                                        'is_required'=>false,'sort_order'=>3],
            ['label'=>'Transmission',  'key'=>'transmission',  'type'=>'select', 'options'=>json_encode(['Automatic','Manual','CVT']),                                'placeholder'=>null, 'is_required'=>false,'sort_order'=>4],
        ],
        // Roommates (id=4)
        4 => [
            ['label'=>'Room Type',     'key'=>'room_type',     'type'=>'select', 'options'=>json_encode(['Private Room','Shared Room','Full Unit']),                  'placeholder'=>null, 'is_required'=>false,'sort_order'=>1],
            ['label'=>'Rent Includes', 'key'=>'rent_includes', 'type'=>'select', 'options'=>json_encode(['Utilities Included','Utilities Extra']),                   'placeholder'=>null, 'is_required'=>false,'sort_order'=>2],
        ],
        // Services (id=3)
        3 => [
            ['label'=>'Service Type',  'key'=>'service_type',  'type'=>'text',   'options'=>null, 'placeholder'=>'e.g. Plumbing, Tutoring',                          'is_required'=>false,'sort_order'=>1],
            ['label'=>'Experience',    'key'=>'experience',    'type'=>'text',   'options'=>null, 'placeholder'=>'e.g. 5 years',                                      'is_required'=>false,'sort_order'=>2],
        ],
    ];

    $added = 0;
    foreach ($classifiedFields as $catId => $fields) {
        // Remove existing fields for this category first
        DB::table('category_fields')->where('category_id', $catId)->delete();
        foreach ($fields as $f) {
            DB::table('category_fields')->insert([
                'category_id' => $catId,
                'label'       => $f['label'],
                'key'         => $f['key'],
                'type'        => $f['type'],
                'options'     => $f['options'],
                'placeholder' => $f['placeholder'],
                'is_required' => $f['is_required'],
                'sort_order'  => $f['sort_order'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            $added++;
        }
        $catName = DB::table('categories')->where('id',$catId)->value('name');
        echo "<p class='ok'>✓ Added " . count($fields) . " fields for <b>{$catName}</b> (id={$catId})</p>";
    }
    echo "<p class='ok'><b>Total {$added} fields added!</b></p>";
    echo '<p><a href="?key=' . KEY . '" style="color:#569cd6">→ Refresh to verify</a></p>';
} else {
    echo '<p>Click to add custom fields for: Real Estate, Buy & Sell, Autos, Roommates, Services</p>';
    echo '<p><a href="?key=' . KEY . '&action=add_fields" style="color:#4ec9b0;font-size:18px;font-weight:bold">→ Add Classified Fields Now</a></p>';
}

// ── Reset admin password ─────────────────────────────────────────────────────
echo '<h3 class="warn">4. Admin User</h3>';
if ($action === 'fix_admin') {
    DB::table('users')->where('email','admin@gobazzar.com')->update([
        'password' => bcrypt('admin123'),
        'email_verified_at' => now(),
    ]);
    echo '<p class="ok">✓ admin@gobazzar.com password set to admin123 and verified</p>';
} else {
    $admin = DB::table('users')->where('email','admin@gobazzar.com')->first();
    if ($admin) {
        echo '<p>Admin exists. Verified: ' . ($admin->email_verified_at ? '<span class="ok">YES</span>' : '<span class="err">NO</span>') . '</p>';
        echo '<p><a href="?key=' . KEY . '&action=fix_admin" style="color:#569cd6">→ Reset password to admin123 + verify email</a></p>';
    } else {
        echo '<p class="err">admin@gobazzar.com NOT found</p>';
    }
}

echo '<br><hr><p class="err">⚠ DELETE this file (setup-fields.php) from server after use!</p>';
echo '</body>';
