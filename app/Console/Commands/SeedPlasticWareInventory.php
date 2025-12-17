<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedPlasticWareInventory extends Command
{
    protected $signature = 'inventory:seed-plastic-ware';

    protected $description = 'Seed plastic ware inventory items';

    public function handle()
    {
        $this->info('Seeding Plastic Ware Inventory...');
        $this->newLine();

        $plasticWareItems = [
            ['item_name' => '4 wells Dish', 'size_dose' => 'box', 'amount' => 120, 'unit_price' => 220],
            ['item_name' => 'Central well Dish (CT)', 'size_dose' => 'box', 'amount' => 500, 'unit_price' => 393],
            ['item_name' => 'Petri Dish (ICSI Dish)', 'size_dose' => 'box', 'amount' => 500, 'unit_price' => 547],
            ['item_name' => 'Small Round Plate', 'size_dose' => 'box', 'amount' => 500, 'unit_price' => 135],
            ['item_name' => 'Big Round Plate', 'size_dose' => 'box', 'amount' => 500, 'unit_price' => 347],
            ['item_name' => 'Conical Tube', 'size_dose' => 'box', 'amount' => 500, 'unit_price' => 153],
            ['item_name' => 'Easy Flask 25 vic', 'size_dose' => 'box', 'amount' => 200, 'unit_price' => 141],
            ['item_name' => '5 ml Tube (small Round butt)', 'size_dose' => 'box', 'amount' => 500, 'unit_price' => 157],
            ['item_name' => '14 ml Tube (Big round butt)', 'size_dose' => 'box', 'amount' => 500, 'unit_price' => 255],
            ['item_name' => '1 ml serological pipette', 'size_dose' => 'Pack', 'amount' => 250, 'unit_price' => 69],
            ['item_name' => '5 ml serological pipette', 'size_dose' => 'Pack', 'amount' => 100, 'unit_price' => 39],
            ['item_name' => '10 ml serological pipette', 'size_dose' => 'Pack', 'amount' => 100, 'unit_price' => 44],
            ['item_name' => 'Eppendorf micro pipette', 'size_dose' => 'box', 'amount' => 100, 'unit_price' => 52],
            ['item_name' => '10 micro pipette tip (sterile)', 'size_dose' => 'box', 'amount' => 0, 'unit_price' => 0, 'status' => 'out of stock'],
            ['item_name' => '1000 microlitre pipette tip', 'size_dose' => 'rack', 'amount' => 100, 'unit_price' => 9],
            ['item_name' => 'Short Pipette Glass', 'size_dose' => '250', 'amount' => 250, 'unit_price' => 198],
            ['item_name' => 'Long Pipette Glass', 'size_dose' => 'box', 'amount' => 250, 'unit_price' => 157],
            ['item_name' => '1 ml Nipro Syringe /Tuberculin', 'size_dose' => 'box', 'amount' => 100, 'unit_price' => 8],
            ['item_name' => '3 ml Nipro Syringe', 'size_dose' => 'box', 'amount' => 100, 'unit_price' => 5],
            ['item_name' => '5 ml Nipro Syringe', 'size_dose' => 'box', 'amount' => 0, 'unit_price' => 0, 'status' => 'unknown'],
            ['item_name' => 'ICSI Needle', 'size_dose' => 'box', 'amount' => 10, 'unit_price' => 314],
            ['item_name' => 'Holding Needle', 'size_dose' => 'box', 'amount' => 10, 'unit_price' => 283],
            ['item_name' => 'Biopsy Needle', 'size_dose' => 'box', 'amount' => 10, 'unit_price' => 314],
            ['item_name' => 'Globet', 'size_dose' => 'Pack', 'amount' => 10, 'unit_price' => 8.5],
            ['item_name' => 'Cryo Aluminium Cane', 'size_dose' => 'Pack', 'amount' => 50, 'unit_price' => 65],
            ['item_name' => 'Cryo Vial 1.8 ml', 'size_dose' => 'Pack', 'amount' => 50, 'unit_price' => 18],
            ['item_name' => 'Cryctech Straw', 'size_dose' => 'Pack', 'amount' => 10, 'unit_price' => 267],
            ['item_name' => 'K-JET7019 ET catheter', 'size_dose' => 'PCS', 'amount' => 1, 'unit_price' => 44],
            ['item_name' => 'Gynetic IUI Catheter', 'size_dose' => 'box', 'amount' => 15, 'unit_price' => 251],
            ['item_name' => '170 Single Lumen/Wallace', 'size_dose' => 'box', 'amount' => 10, 'unit_price' => 503],
            ['item_name' => 'Double lumen /COOK', 'size_dose' => 'PCS', 'amount' => 1, 'unit_price' => 59],
            ['item_name' => 'Stripper Tip 145', 'size_dose' => 'Pack', 'amount' => 10, 'unit_price' => 63],
            ['item_name' => 'Tube load', 'size_dose' => 'Pack', 'amount' => 0, 'unit_price' => 0, 'status' => 'Free'],
            ['item_name' => 'PBS', 'size_dose' => 'Pack', 'amount' => 0, 'unit_price' => 0, 'status' => 'Free'],
            ['item_name' => 'Sterile Water', 'size_dose' => 'bottle', 'amount' => 1, 'unit_price' => 1.8],
            ['item_name' => 'Minisart syringe filter', 'size_dose' => 'box', 'amount' => 50, 'unit_price' => 94],
            ['item_name' => 'Sterile Bottle For BT', 'size_dose' => 'bottle', 'amount' => 1, 'unit_price' => 94],
            ['item_name' => 'Len Paper', 'size_dose' => 'box', 'amount' => 1, 'unit_price' => 100],
            ['item_name' => '3M Parafilm', 'size_dose' => 'box', 'amount' => 1, 'unit_price' => 31],
            ['item_name' => 'PH paper (Sperm)', 'size_dose' => 'box', 'amount' => 1, 'unit_price' => 25],
            ['item_name' => 'Sperm Container/Millionant', 'size_dose' => 'box', 'amount' => 300, 'unit_price' => 69],
            ['item_name' => 'Sterile Guaze Pad', 'size_dose' => 'box', 'amount' => 100, 'unit_price' => 8],
            ['item_name' => 'Spare cork and cover for LAB30', 'size_dose' => 'pcs', 'amount' => 1, 'unit_price' => 168],
        ];

        $created = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            foreach ($plasticWareItems as $itemData) {
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
                    'description' => 'Plastic ware for laboratory use',
                    'category' => 'Plastic Ware',
                    'type_of_supply' => \App\Enums\SupplyTypeEnum::LAB_SUPPLY,
                    'quantity' => $itemData['amount'],
                    'unit' => $itemData['size_dose'],
                    'minimum_stock' => max(5, (int) ($itemData['amount'] * 0.1)),
                    'unit_price' => $itemData['unit_price'],
                    'selling_price' => $itemData['unit_price'],
                    'supplier' => 'AFC IVF Stock',
                    'location' => 'Lab Storage',
                    'notes' => $itemData['status'] ?? null,
                ]);

                $created++;
            }

            DB::commit();

            $this->newLine();
            $this->info("✓ Successfully created {$created} plastic ware items");

            if ($skipped > 0) {
                $this->warn("⚠ Skipped {$skipped} items (already exist)");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error seeding plastic ware: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
