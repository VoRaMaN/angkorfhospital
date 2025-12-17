<?php

namespace Database\Seeders;

use App\Enums\SupplyTypeEnum;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

class PatchItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // Medications
            [
                'item_name' => 'Metformin 500mg',
                'description' => 'Diabetes medication',
                'category' => 'Oral Hypoglycemic',
                'type_of_supply' => SupplyTypeEnum::MEDICATION,
                'quantity' => 1000,
                'unit' => 'tablet',
                'unit_price' => 0.50,
                'selling_price' => 1.00,
            ],
            [
                'item_name' => 'Lisinopril 10mg',
                'description' => 'Blood pressure medication',
                'category' => 'ACE Inhibitor',
                'type_of_supply' => SupplyTypeEnum::MEDICATION,
                'quantity' => 500,
                'unit' => 'tablet',
                'unit_price' => 0.75,
                'selling_price' => 1.50,
            ],
            [
                'item_name' => 'Atorvastatin 20mg',
                'description' => 'Cholesterol medication',
                'category' => 'Statin',
                'type_of_supply' => SupplyTypeEnum::MEDICATION,
                'quantity' => 750,
                'unit' => 'tablet',
                'unit_price' => 1.25,
                'selling_price' => 2.50,
            ],

            // Lab Supplies
            [
                'item_name' => 'Blood Glucose Test Strip',
                'description' => 'For blood sugar testing',
                'category' => 'Diagnostic',
                'type_of_supply' => SupplyTypeEnum::LAB_SUPPLY,
                'quantity' => 2000,
                'unit' => 'strip',
                'unit_price' => 0.30,
                'selling_price' => 0.75,
            ],
            [
                'item_name' => 'HbA1c Test Kit',
                'description' => 'Hemoglobin A1C test',
                'category' => 'Diagnostic',
                'type_of_supply' => SupplyTypeEnum::LAB_SUPPLY,
                'quantity' => 100,
                'unit' => 'kit',
                'unit_price' => 15.00,
                'selling_price' => 25.00,
            ],
            [
                'item_name' => 'Lipid Panel Test',
                'description' => 'Cholesterol screening',
                'category' => 'Diagnostic',
                'type_of_supply' => SupplyTypeEnum::LAB_SUPPLY,
                'quantity' => 200,
                'unit' => 'test',
                'unit_price' => 12.00,
                'selling_price' => 20.00,
            ],

            // Medical Equipment
            [
                'item_name' => 'Digital Blood Pressure Monitor',
                'description' => 'Automatic BP monitor',
                'category' => 'Monitoring Equipment',
                'type_of_supply' => SupplyTypeEnum::MEDICAL_EQUIPMENT,
                'quantity' => 20,
                'unit' => 'unit',
                'unit_price' => 45.00,
                'selling_price' => 75.00,
            ],
            [
                'item_name' => 'Glucometer',
                'description' => 'Blood glucose meter',
                'category' => 'Monitoring Equipment',
                'type_of_supply' => SupplyTypeEnum::MEDICAL_EQUIPMENT,
                'quantity' => 50,
                'unit' => 'unit',
                'unit_price' => 25.00,
                'selling_price' => 45.00,
            ],

            // RX Medicines
            [
                'item_name' => 'Insulin Glargine (Lantus)',
                'description' => 'Long-acting insulin',
                'category' => 'Insulin',
                'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                'quantity' => 100,
                'unit' => 'vial',
                'dose_unit' => 'units',
                'unit_price' => 75.00,
                'selling_price' => 125.00,
            ],
            [
                'item_name' => 'Insulin Aspart (NovoLog)',
                'description' => 'Rapid-acting insulin',
                'category' => 'Insulin',
                'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                'quantity' => 80,
                'unit' => 'vial',
                'dose_unit' => 'units',
                'unit_price' => 85.00,
                'selling_price' => 140.00,
            ],

            // Office Supplies
            [
                'item_name' => 'Patient Education Booklet - Diabetes',
                'description' => 'Diabetes management guide',
                'category' => 'Educational Material',
                'type_of_supply' => SupplyTypeEnum::OFFICE_SUPPLY,
                'quantity' => 500,
                'unit' => 'booklet',
                'unit_price' => 2.00,
                'selling_price' => 5.00,
            ],
            [
                'item_name' => 'Medication Log Sheet',
                'description' => 'Patient tracking form',
                'category' => 'Forms',
                'type_of_supply' => SupplyTypeEnum::OFFICE_SUPPLY,
                'quantity' => 1000,
                'unit' => 'sheet',
                'unit_price' => 0.10,
                'selling_price' => 0.25,
            ],
        ];

        foreach ($items as $item) {
            Inventory::updateOrCreate(
                ['item_name' => $item['item_name']],
                $item
            );
        }
    }
}
