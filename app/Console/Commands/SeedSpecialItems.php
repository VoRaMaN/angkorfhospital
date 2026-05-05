<?php

namespace App\Console\Commands;

use App\Models\SpecialItem;
use Illuminate\Console\Command;

class SeedSpecialItems extends Command
{
    protected $signature = 'special-items:seed {--dry-run : Preview without saving}';

    protected $description = 'Seed or update special items with custom prices';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $items = [
            ['name' => 'DF 15', 'unit_price' => 15.00],
            ['name' => 'TVS 25', 'unit_price' => 25.00],
            ['name' => 'Delivery4$', 'unit_price' => 4.00],
            ['name' => 'Ultrasound 15', 'unit_price' => 15.00],
            ['name' => 'Delivery 3$', 'unit_price' => 3.00],
            ['name' => 'DF $25', 'unit_price' => 25.00],
            ['name' => 'Start Stimulate 2500$ (PK 5555$)Include 1time ET/FET', 'unit_price' => 2500.00],
            ['name' => 'Biopsy Anapat 50', 'unit_price' => 50.00],
            ['name' => 'Saction Endometrial with TIVA', 'unit_price' => 350.00],
            ['name' => 'Cryotheraphy 120$', 'unit_price' => 120.00],
            ['name' => 'Suction Fluid 50', 'unit_price' => 50.00],
            ['name' => 'Completed payment4000$ IVF PK 7500$ FET/ET', 'unit_price' => 4000.00],
            ['name' => 'NIPT 5', 'unit_price' => 300.00],
            ['name' => 'MVA 200$', 'unit_price' => 200.00],
            ['name' => 'Booking 2000$ IVF PK 20000$', 'unit_price' => 2000.00],
            ['name' => 'FET 1850$', 'unit_price' => 1850.00],
            ['name' => 'Ex-Em kits 150$', 'unit_price' => 150.00],
            ['name' => 'Gardasil 9 190$', 'unit_price' => 190.00],
            ['name' => 'Pay complete ANC VVIP 233$', 'unit_price' => 233.00],
            ['name' => 'IUI PK 780$ (Follisurge 900IU, TVS, Hormone test)', 'unit_price' => 780.00],
            ['name' => 'Pay Complete IVF3900$ (PK 7400$)Include 1time ET/FET , Eggs wash', 'unit_price' => 3900.00],
            ['name' => 'PTG-A Small Pk', 'unit_price' => 4000.00],
            ['name' => 'NIPT 24 (400$)', 'unit_price' => 400.00],
            ['name' => 'Start stimulate payment 3500$ (IVF PK 7000$)ET/FET 1time', 'unit_price' => 3500.00],
            ['name' => 'Suction 60$', 'unit_price' => 60.00],
            ['name' => 'Booking 200$ ANC PK 468$', 'unit_price' => 200.00],
            ['name' => 'Booking 100$ for Gadasil 3 dose price 484.5$ (15% discount )', 'unit_price' => 100.00],
            ['name' => 'Pay complete 7500$ IVF PK 15000$', 'unit_price' => 7500.00],
            ['name' => 'Proluton', 'unit_price' => 18.00],
            ['name' => 'Second payment 5250$ IVF PK 12500$(ET/FET 1TIME)', 'unit_price' => 5250.00],
            ['name' => 'FET 900', 'unit_price' => 900.00],
            ['name' => 'PESA/TESE 550$', 'unit_price' => 550.00],
            ['name' => 'Start Stimulate 3000$ pk 7500 include ET/FET 1 time', 'unit_price' => 3000.00],
            ['name' => 'Booking 2500$ IVF pk 5555$', 'unit_price' => 2500.00],
            ['name' => 'Completed payment 2000$ IVF PK 5000$ FET/ET', 'unit_price' => 2000.00],
            ['name' => 'Start Stimulate 3500$ (PK 7000$)Include 1time ET/FET', 'unit_price' => 3500.00],
        ];

        $this->info('Seeding special items...');
        $this->newLine();

        $created = 0;
        $updated = 0;

        foreach ($items as $item) {
            $existing = SpecialItem::where('name', $item['name'])->first();

            if ($isDryRun) {
                $status = $existing ? 'update' : 'create';
                $this->line("  [{$status}] {$item['name']} — \${$item['unit_price']}");

                continue;
            }

            $result = SpecialItem::updateOrCreate(
                ['name' => $item['name']],
                ['unit_price' => $item['unit_price'], 'is_active' => true]
            );

            if ($result->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn('DRY RUN — no changes saved.');

            return Command::SUCCESS;
        }

        $this->info("✓ Created: {$created} | Updated: {$updated} special items.");

        return Command::SUCCESS;
    }
}
