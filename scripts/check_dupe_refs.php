<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Duplicate pairs (higher ID = the duplicate to potentially remove)
$duplicates = [
    ['keep' => 239, 'remove' => 280, 'name' => 'CA 125'],
    ['keep' => 202, 'remove' => 274, 'name' => 'HBs Ag'],
];

foreach ($duplicates as $d) {
    echo "--- {$d['name']} (keep ID {$d['keep']}, check ID {$d['remove']}) ---\n";

    // Check medical_orders
    $mo = DB::select('SELECT COUNT(*) as cnt FROM medical_orders WHERE inventory_id = ?', [$d['remove']]);
    echo "  medical_orders refs: " . $mo[0]->cnt . "\n";

    // Check billings or billing_items if exists
    $tables = DB::select("SHOW TABLES LIKE '%billing%'");
    foreach ($tables as $t) {
        $tableName = array_values((array)$t)[0];
        $cols = DB::select("SHOW COLUMNS FROM `{$tableName}` LIKE 'inventory_id'");
        if (!empty($cols)) {
            $cnt = DB::select("SELECT COUNT(*) as cnt FROM `{$tableName}` WHERE inventory_id = ?", [$d['remove']]);
            echo "  {$tableName} refs: " . $cnt[0]->cnt . "\n";
        }
    }

    echo "\n";
}
