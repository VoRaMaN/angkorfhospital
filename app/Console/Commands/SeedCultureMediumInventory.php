<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedCultureMediumInventory extends Command
{
    protected $signature = 'inventory:seed-culture-medium';

    protected $description = 'Seed culture medium inventory items';

    public function handle()
    {
        $this->info('Seeding Culture Medium Inventory...');
        $this->newLine();

        $cultureMediumItems = [
            ['item_name' => 'Sage 1 step', 'size_dose' => 'bottle', 'amount' => 60, 'unit_price' => 311],
            ['item_name' => 'Irvine-CSCM', 'size_dose' => 'bottle', 'amount' => 20, 'unit_price' => 226],
            ['item_name' => 'Sperm preparation', 'size_dose' => 'bottle', 'amount' => 60, 'unit_price' => 141],
            ['item_name' => 'Flushing Medium', 'size_dose' => 'bottle', 'amount' => 100, 'unit_price' => 59],
            ['item_name' => 'Aspiration Medium', 'size_dose' => 'bottle', 'amount' => 100, 'unit_price' => 44],
            ['item_name' => 'Hyaluronidase', 'size_dose' => 'bottle', 'amount' => 10, 'unit_price' => 63],
            ['item_name' => 'PVP 10%', 'size_dose' => 'bottle', 'amount' => 1, 'unit_price' => 63],
            ['item_name' => 'Vitrification (Freezing)/KITAZATO', 'size_dose' => 'box', 'amount' => 0, 'unit_price' => 189],
            ['item_name' => 'Thawing /KITAZATO', 'size_dose' => 'box', 'amount' => 0, 'unit_price' => 141],
            ['item_name' => 'Sperm Freezing', 'size_dose' => 'box', 'amount' => 20, 'unit_price' => 63],
            ['item_name' => 'Sil Selection Set', 'size_dose' => '1 Set', 'amount' => 0, 'unit_price' => 440],
            ['item_name' => 'Lite Oil', 'size_dose' => 'bottle', 'amount' => 500, 'unit_price' => 313],
        ];

        $created = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            foreach ($cultureMediumItems as $itemData) {
                $existingItem = Inventory::where('item_name', $itemData['item_name'])
                    ->where('type_of_supply', \App\Enums\SupplyTypeEnum::LAB_SUPPLY)
                    ->first();

                if ($existingItem) {
                    $this->warn("Item already exists: {$itemData['item_name']}");
                    $skipped++;

                    continue;
                }

                Inventory::create([
                    'item_name' => $itemData['item_name'],
                    'description' => 'Culture medium for laboratory use',
                    'category' => 'Culture Medium',
                    'type_of_supply' => \App\Enums\SupplyTypeEnum::LAB_SUPPLY,
                    'quantity' => $itemData['amount'],
                    'unit' => $itemData['size_dose'],
                    'minimum_stock' => max(5, (int) ($itemData['amount'] * 0.1)),
                    'unit_price' => $itemData['unit_price'],
                    'selling_price' => $itemData['unit_price'],
                    'supplier' => 'AFC IVF Stock',
                    'location' => 'Lab Storage',
                ]);

                $created++;
            }

            DB::commit();

            $this->newLine();
            $this->info("✓ Successfully created {$created} culture medium items");

            if ($skipped > 0) {
                $this->warn("⚠ Skipped {$skipped} items (already exist)");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error seeding culture medium: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
