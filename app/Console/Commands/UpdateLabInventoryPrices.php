<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateLabInventoryPrices extends Command
{
    protected $signature = 'inventory:update-lab-prices {--dry-run : Preview changes without updating}';

    protected $description = 'Update lab inventory selling prices';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting Lab Inventory Price Update...');
        $this->newLine();

        // Define all price updates (excluding items with $0.00)
        $priceUpdates = [
            // Blood Lab
            'Albumine' => 3.00,
            'Allergy Total IgE' => 15.00,
            'AMH' => 15.00,
            'Anapath' => 45.00,
            'Anti HCV $5' => 5.00,
            'Anti-HBs' => 15.00,
            'Anti-HBs (quantitative)' => 10.00,
            'Anti-HCV' => 15.00,
            'Anti-HIV' => 15.00,
            'ASLOW' => 2.00,
            'Beta HCG' => 15.00,
            'Bilirubine' => 5.00,
            'Biopsy (Anapath)' => 50.00,
            'Biopsy Endometre' => 70.00,
            'Biopsy EM' => 75.00,
            'Blood ABO/Rh' => 3.00,
            'Blood Culture' => 25.00,
            'Blood sugar' => 2.00,
            'Body Fluid Culture' => 25.00,
            'C-Reactive Protein (CRP)' => 5.00,
            'CA 119' => 15.00,
            'CA 125' => 20.00, // Using $20 as confirmed
            'CA 19.9' => 20.00,
            'Calcium' => 3.00,
            'CBC' => 2.00,
            'CBC, Anti HBs, HBs Ag, VDRL, HIV' => 32.00,
            'CD4' => 25.00,
            'CEA (Carcinoembryonic antigen)' => 15.00,
            'Cervical Swab' => 20.00,
            'Chlamydia IgG' => 15.00,
            'Cholesterol HDL' => 3.00,
            'Cholesterol LDL' => 3.00,
            'Cholesterol Total' => 3.00,
            'Clamydia Trachomatis' => 15.00,
            'Clamydia Trachomatis/N.Gonorrhoea' => 30.00,
            'Creatinine' => 2.00,
            'CRP (C-Reactive Protein)' => 5.00,
            'Dengue Fever Ag' => 5.00,
            'E2' => 15.00,
            'Electrolyte (Na, K, Cl)' => 15.00,
            'Electrophoresis-Hb' => 30.00,
            'ESR' => 2.00,
            'ESR/Vs' => 2.00,
            'FSH' => 15.00,
            'G6PD' => 20.00,
            'GBS Screening' => 30.00,
            'GGT' => 2.00,
            'Harmony $700' => 700.00,
            'Harmony $800' => 800.00,
            'HbA1C' => 15.00,
            'HBs Ag' => 15.00,
            'Hbs Ag' => 5.00,
            'Helicobacter Pylori' => 10.00,
            'Hemoglobin typing' => 29.00,
            'Hep B virus load' => 60.00,
            'HIV = $5' => 5.00,
            'HPV 6,11' => 80.00,
            'HPV/DNA test' => 60.00,
            'INR/PT (GY)' => 10.00,
            'Iron/Ferritin (Blood Test)' => 15.00,
            'J236' => 150.00,
            'LAB Packages-CA125+CA19.9' => 100.00,
            'LH' => 15.00,
            'Magnesium' => 5.00,
            'Malaria Smear' => 2.00,
            'Mynids' => 800.00,
            'NIPT all Types 400$' => 400.00,
            'NIPT 5 Types 300$' => 300.00,
            'Progesterone' => 15.00,
            'PROLACTIN' => 15.00,
            'Pus Culture' => 25.00,
            'RPR' => 15.00,
            'Rubella Ig G' => 17.00,
            'Stool Culture' => 25.00,
            'T3' => 15.00,
            'T4' => 15.00,
            'TB Anti-AgG' => 15.00,
            'TCA' => 15.00,
            'Testosterone' => 20.00,
            'Thin Prep' => 150.00,
            'Toxoplasma IgG' => 20.00,
            'Toxoplasma IgM' => 20.00,
            'TPHA' => 15.00,
            'Transaminase (ASAT, ALAT)' => 2.00,
            'Transaminase' => 2.00,
            'Triglyceride' => 2.00,
            'TSH' => 15.00,
            'Urea' => 2.00,
            'Uric Acid' => 2.00,
            'Urine Culture' => 20.00,
            'Urine test' => 5.00,
            'VDRL (FTA-ABs-IGg)' => 15.00,
            'VDRL (FTA-ABs-IgM)' => 15.00,
            'VDRL (RPR)' => 5.00,
            'Viral load Hep B' => 50.00,
            'Vit B12' => 25.00,
            'Vitamin D Total' => 40.00,
            // Skipping: X Fragile - $0.00

            // Sperm Lab
            'DF' => 500.00,
            'Frozen Sperm Package (Sperm freeze + Blood test)' => 250.00,
            'HA Assist sperm selection' => 450.00,
            'Sperm Analysis' => 30.00,
            'Sperm Extra 1 vial' => 30.00,
            'Sperm Freezing (1yr)' => 155.00,
            'Sperm Freezing (1yr) × 2 times' => 250.00,
            'Sperm Selection XY' => 130.00,
            'Sperm Storage per Straw per Year' => 40.00,
            'Sperm Wash + Storage + DF' => 3000.00,
            'Storage' => 1000.00,
            'Vial per year' => 30.00,
            'Washing Sperm Precaution (HIV)' => 3000.00,
            'Washing Sperm Precaution (VDRL & Hep B)' => 700.00,

            // Embryo Lab
            'Assisted Hatching' => 250.00,
            'Egg Sharing Pack' => 3300.00,
            'Embryo Glue' => 300.00,
            'Embryo Storage per Embryo per Year' => 40.00,
            'Embryo Storage per Embryo per Year — Promotion' => 30.00,
            'Embryo Thawing' => 300.00,
            // Skipping: Embryo transfer from other center - $0.00
            'Freezing Day 1 (16-Embryo / 4-Straw)' => 900.00,
            'Freezing Day 3 (16-Embryo / 4-Straw)' => 900.00,
            'Freezing: Extra per straw' => 60.00,
            'Freezing Blastocyst (8-Embryo / 8-Straw)' => 900.00,
            'Freezing Blastocyst (8-Embryo / 8-Straw) for 2 Packs' => 1800.00,
            // Skipping: Freezing oocyte (12E, 4S) - $0.00
            'Oocyte Storage per year' => 40.00,
            'SEPARATE TANK FOR EMBRYO PRECAUTION' => 1500.00,
            'Special Biopsy' => 300.00,
            'Solution for biopsy (day 6)' => 150.00,
            'Washing Egg Precaution' => 700.00,

            // Genetic Lab
            // Skipping: a-CGH(4 embryos) report 2 weeks - $0.00
            'a-NGS (Up to 10 Embryo)' => 5000.00,
            'NGS Extra per Embryo' => 450.00,
            'Pay extra for CGH (Promotion PGD Package)' => 1000.00,
            'PGD (Up to 10 Embryo)' => 4500.00,
            'PGD Extra per Embryo' => 300.00,
            'PTG-A (Up to 10 Embryo)' => 5000.00,
            'PTG-A Extra per Embryo' => 450.00,
            'PTG-A (1-5 Embryos)' => 4000.00,
            'Thawing Media' => 460.00,
        ];

        $updated = 0;
        $notFound = [];
        $changes = [];

        DB::beginTransaction();

        try {
            foreach ($priceUpdates as $itemName => $newPrice) {
                $item = Inventory::where('type_of_supply', 'lab_supply')
                    ->where('item_name', $itemName)
                    ->first();

                if ($item) {
                    $oldPrice = $item->selling_price;

                    if ($oldPrice != $newPrice) {
                        $changes[] = [
                            'id' => $item->id,
                            'name' => $itemName,
                            'old_price' => $oldPrice,
                            'new_price' => $newPrice,
                        ];

                        if (! $isDryRun) {
                            $item->update(['selling_price' => $newPrice]);
                        }

                        $updated++;
                    }
                } else {
                    $notFound[] = $itemName;
                }
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->warn('DRY RUN MODE - No changes were made');
                $this->newLine();
            } else {
                DB::commit();
            }

            // Display results
            if (! empty($changes)) {
                $this->info("Items to be updated: {$updated}");
                $this->newLine();

                $this->table(
                    ['ID', 'Item Name', 'Old Price', 'New Price'],
                    array_map(fn ($change) => [
                        $change['id'],
                        $change['name'],
                        '$'.number_format($change['old_price'], 2),
                        '$'.number_format($change['new_price'], 2),
                    ], array_slice($changes, 0, 20)) // Show first 20
                );

                if (count($changes) > 20) {
                    $this->info('... and '.(count($changes) - 20).' more items');
                }
            } else {
                $this->info('No price changes needed - all items already have correct prices');
            }

            if (! empty($notFound)) {
                $this->newLine();
                $this->warn('Items not found in inventory:');
                foreach ($notFound as $item) {
                    $this->line("  - {$item}");
                }
            }

            $this->newLine();

            if ($isDryRun) {
                $this->info('To apply these changes, run: php artisan inventory:update-lab-prices');
            } else {
                $this->info("✓ Successfully updated {$updated} lab inventory prices!");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error updating prices: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
