<?php

namespace Database\Seeders;

use App\Enums\SupplyTypeEnum;
use App\Models\Inventory;
use App\Models\LabPanel;
use App\Models\LabPanelItem;
use Illuminate\Database\Seeder;

class LabPackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bloodLabItems = [
            'E2', 'LH', 'FSH', 'PROLACTIN', 'Beta HCG', 'Blood sugar', 'HbA1C', 'Cholesterol Total',
            'Triglyceride', 'Testosterone', 'X Fragile', 'CBC', 'Anti-HCV', 'Anti-HBs', 'HBs Ag',
            'Anti-HIV', 'VDRL (FTA-ABs-IGg)', 'VDRL (FTA-ABs-IgM)', 'T3', 'T4', 'G6PD', 'Creatinine',
            'Urine test', 'Hemoglobin typing', 'Electrolyte (Na, K, Cl)', 'Blood ABO/Rh', 'CD4',
            'LAB Packages-CA125+CA19.9', 'Rubella Ig G', 'Iron/Ferritin (Blood Test)', 'Chlamydia IgG',
            'GBS Screening', 'Dengue Fever Ag', 'Calcium', 'Clamydia Trachomatis', 'Cholesterol HDL',
            'Cholesterol LDL', 'Helicobacter Pylori', 'Magnesium', 'TB Anti-AgG', 'Transaminase',
            'CEA (Carcinoembryonic antigen)', 'Blood Culture', 'INR/PT (GY)', 'Bilirubine', 'GGT',
            'Transaminase (ASAT, ALAT)', 'Body Fluid Culture', 'Stool Culture', 'Urine Culture',
            'Pus Culture', 'CA 125', 'CA 19.9', 'C-Reactive Protein (CRP)', 'Electrophoresis-Hb',
            'ESR/Vs', 'TSH', 'Urea', 'Uric Acid', 'HPV/DNA test', 'Cervical Swab', 'Biopsy (Anapath)',
            'Clamydia Trachomatis/N.Gonorrhoea', 'TPHA', 'RPR', 'Malaria Smear', 'Progesterone',
            'Anapath', 'AMH', 'Albumine', 'ASLOW', 'Biopsy EM', 'Allergy Total IgE', 'Toxoplasma IgM',
            'Toxoplasma IgG', 'Hep B virus load', 'Thin Prep', 'TCA', 'Viral load Hep B',
            'Anti-HBs (quantitative)', 'Biopsy Endometre', 'VDRL (RPR)', 'HIV = $5', 'ESR',
            'CBC, Anti HBs, HBs Ag, VDRL, HIV', 'Anti HCV $5', 'HBs Ag', 'HPV 6,11', 'Harmony $700',
            'Harmony $800', 'J236', 'CRP (C-Reactive Protein)', 'CA 125', 'CA 119', 'Mynids',
        ];

        $spermLabItems = [
            'Sperm Analysis', 'Sperm Freezing (1yr)', 'Sperm Selection XY', 'HA Assist sperm selection',
            'Sperm Storage per Straw per Year', 'Sperm Extra 1 vial', 'Vial per year',
            'Frozen Sperm Package (Sperm freeze + Blood test)', 'Washing Sperm Precaution (VDRL & Hep B)',
            'Storage', 'DF', 'Sperm Freezing (1yr) × 2 times', 'Sperm Wash + Storage + DF',
            'Washing Sperm Precaution (HIV)',
        ];

        $embryoLabItems = [
            'Freezing Day 1 (16-Embryo / 4-Straw)', 'Freezing Day 3 (16-Embryo / 4-Straw)',
            'Freezing: Extra per straw', 'Assisted Hatching', 'Embryo transfer from other center',
            'Embryo Thawing', 'Embryo Glue', 'Embryo Storage per Embryo per Year', 'Special Biopsy',
            'Freezing oocyte (12E, 4S)', 'Solution for biopsy (day 6)', 'Freezing Blastocyst (8-Embryo / 8-Straw)',
            'SEPARATE TANK FOR EMBRYO PRECAUTION', 'Freezing Blastocyst (8-Embryo / 8-Straw) for 2 Packs',
            'Egg Sharing Pack', 'Embryo Storage per Embryo per Year — Promotion', 'Washing Egg Precaution',
            'Oocyte Storage per year',
        ];

        $geneticLabItems = [
            'NGS Extra per Embryo', 'a-NGS (Up to 10 Embryo)', 'PGD Extra per Embryo', 'Thawing Media',
            'a-CGH (4 embryos) report 2 weeks', 'PGD (Up to 10 Embryo)', 'Pay extra for CGH (Promotion PGD Package)',
        ];

        // Create inventory items
        $allItems = array_merge($bloodLabItems, $spermLabItems, $embryoLabItems, $geneticLabItems);
        $inventories = [];

        foreach ($allItems as $itemName) {
            $inventory = Inventory::create([
                'item_name' => $itemName,
                'description' => "Lab test: {$itemName}",
                'category' => 'Lab Test',
                'type_of_supply' => SupplyTypeEnum::LAB_SUPPLY,
                'quantity' => 1000, // Default high quantity
                'unit' => 'test',
                'minimum_stock' => 10,
                'unit_price' => 10.00,
                'selling_price' => 15.00,
            ]);
            $inventories[$itemName] = $inventory;
        }

        // Create lab panels
        $panels = [
            'Blood Lab' => $bloodLabItems,
            'Sperm Lab' => $spermLabItems,
            'Embryo Lab' => $embryoLabItems,
            'Genetic Lab' => $geneticLabItems,
        ];

        foreach ($panels as $panelName => $items) {
            $panel = LabPanel::create([
                'name' => $panelName,
                'description' => "Comprehensive {$panelName} package",
                'price' => count($items) * 15.00, // Simple pricing
                'is_active' => true,
            ]);

            foreach ($items as $itemName) {
                LabPanelItem::create([
                    'lab_panel_id' => $panel->id,
                    'inventory_id' => $inventories[$itemName]->id,
                    'quantity_required' => 1,
                    'notes' => 'Standard test',
                ]);
            }
        }
    }
}
