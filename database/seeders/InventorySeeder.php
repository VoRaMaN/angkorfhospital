<?php

namespace Database\Seeders;

use App\Enums\SupplyTypeEnum;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'item_name' => 'Acide Folique 5mg',
                'category' => 'Tablet',
                'barcode' => 'NE0110',
                'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                'quantity' => 500,
                'unit' => 'box',
                'dose_unit' => 'គ្រាប់',
                'total_per_box' => 3000,
                'minimum_stock' => 1000,
                'unit_price' => 0.00,
                'selling_price' => 0.04,
                'expiry_date' => '2028-08-25',
                'alert_days' => 90,
            ],
            [
                'item_name' => 'Albendazole 400mg',
                'category' => 'Tablet',
                'barcode' => 'NE0113',
                'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                'quantity' => 10000,
                'unit' => 'box',
                'dose_unit' => 'គ្រាប់',
                'total_per_box' => 10,
                'minimum_stock' => 5,
                'unit_price' => 0.00,
                'selling_price' => 1.50,
                'expiry_date' => '2026-02-25',
                'alert_days' => 90,
            ],
            [
                'item_name' => 'Albumin',
                'category' => 'Bottle',
                'barcode' => null,
                'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                'quantity' => 0,
                'unit' => 'box',
                'dose_unit' => 'Bottle',
                'total_per_box' => 5,
                'minimum_stock' => 2,
                'unit_price' => 0.00,
                'selling_price' => 70.00,
                'expiry_date' => '2026-03-25',
                'alert_days' => 90,
            ],
            [
                'item_name' => 'Alphachymoral (1box=30tb)',
                'category' => 'Tablet',
                'barcode' => null,
                'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                'quantity' => 0,
                'unit' => 'box',
                'dose_unit' => 'គ្រាប់',
                'total_per_box' => 30,
                'minimum_stock' => 10,
                'unit_price' => 0.00,
                'selling_price' => 0.08,
                'expiry_date' => '2026-08-25',
                'alert_days' => 90,
            ],
        ];

        foreach ($medicines as $medicine) {
            Inventory::create($medicine);
        }
    }
}
