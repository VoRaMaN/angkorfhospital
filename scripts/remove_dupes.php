<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Keep the older (lower) ID, delete the newer duplicate
$toDelete = [280, 274]; // CA 125 dupe, HBs Ag dupe

DB::beginTransaction();

try {
    foreach ($toDelete as $id) {
        $item = DB::selectOne('SELECT id, item_name, type_of_supply FROM inventories WHERE id = ?', [$id]);
        if ($item) {
            DB::delete('DELETE FROM inventories WHERE id = ?', [$id]);
            echo "Deleted duplicate: ID {$item->id} — {$item->item_name} ({$item->type_of_supply})\n";
        }
    }

    DB::commit();
    echo "\nDone.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
