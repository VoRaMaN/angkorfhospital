<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$items = DB::select(
    'SELECT id, item_name, quantity, selling_price, created_at
     FROM inventories
     WHERE type_of_supply = "rx_medicine"
     ORDER BY item_name'
);

echo count($items) . " total RX medicine items:\n\n";
printf("%-6s %-50s %8s %8s  %s\n", 'ID', 'item_name', 'qty', 'price', 'created_at');
echo str_repeat('-', 100) . "\n";
foreach ($items as $i) {
    printf("%-6s %-50s %8s %8s  %s\n",
        $i->id, $i->item_name, $i->quantity, $i->selling_price, $i->created_at
    );
}
