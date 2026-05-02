<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Duplicate pairs: keep first, remove second
$pairs = [
    ['keep' => 239, 'remove' => 280, 'name' => 'CA 125'],
    ['keep' => 202, 'remove' => 274, 'name' => 'HBs Ag'],
];

foreach ($pairs as $p) {
    $refs = DB::select(
        'SELECT COUNT(*) as cnt FROM medical_order_inventory WHERE inventory_id = ?',
        [$p['remove']]
    );
    echo "{$p['name']} — ID {$p['remove']} referenced in medical_order_inventory: " . $refs[0]->cnt . "\n";
}
