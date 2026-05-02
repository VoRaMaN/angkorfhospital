<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$dupes = DB::select(
    'SELECT item_name, COUNT(*) as cnt, GROUP_CONCAT(id ORDER BY id SEPARATOR "|") as ids
     FROM inventories
     WHERE type_of_supply = "rx_medicine"
     GROUP BY item_name
     HAVING cnt > 1'
);

if (empty($dupes)) {
    echo "No duplicates found.\n";
} else {
    echo count($dupes) . " duplicate item(s) found:\n\n";
    foreach ($dupes as $d) {
        echo "  [{$d->ids}]  {$d->item_name}\n";
    }
}
