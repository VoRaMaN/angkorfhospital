<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \Illuminate\Support\Facades\DB::table('inventories')
    ->where('type_of_supply', 'rx_medicine')
    ->orderBy('item_name')
    ->get(['id', 'item_name', 'selling_price']);

foreach ($items as $item) {
    echo $item->id.' | '.$item->item_name.' | $'.$item->selling_price.PHP_EOL;
}
