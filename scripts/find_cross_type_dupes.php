<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Items existing under multiple supply types
$crossType = DB::select(
    'SELECT a.id as a_id, a.item_name, a.type_of_supply as a_type, a.quantity as a_qty, a.selling_price as a_price,
            b.id as b_id, b.type_of_supply as b_type, b.quantity as b_qty, b.selling_price as b_price
     FROM inventories a
     JOIN inventories b ON a.item_name = b.item_name AND a.id != b.id
     ORDER BY a.item_name, a.id'
);

if (empty($crossType)) {
    echo "No items found with the same item_name across any entries.\n";
} else {
    echo count($crossType) . " cross-type or cross-entry duplicates:\n\n";
    printf("%-6s %-45s %-20s %5s %8s | %-6s %-20s %5s %8s\n",
        'ID A', 'item_name', 'Type A', 'Qty', 'Price', 'ID B', 'Type B', 'Qty', 'Price');
    echo str_repeat('-', 140) . "\n";
    foreach ($crossType as $r) {
        printf("%-6s %-45s %-20s %5s %8s | %-6s %-20s %5s %8s\n",
            $r->a_id, $r->item_name, $r->a_type, $r->a_qty, $r->a_price,
            $r->b_id, $r->b_type, $r->b_qty, $r->b_price
        );
    }
}
