<?php

namespace Database\Seeders;

use App\Models\RxMedicine;
use Illuminate\Database\Seeder;

class RxMedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            ['name' => 'Acide Folique 5mg', 'unit_price' => 0.04, 'stock_quantity' => 30, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 3000, 'reorder_quantity' => 1000, 'expiry_date' => '2028-08-01'],
            ['name' => 'Albendazole 400mg', 'unit_price' => 1.50, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 10, 'reorder_quantity' => 5, 'expiry_date' => '2026-02-01'],
            ['name' => 'Albumin', 'unit_price' => 70.00, 'stock_quantity' => 90, 'category' => 'Bottle', 'unit' => 'box', 'dose_unit' => 'Bottle', 'total_per_box' => 5, 'reorder_quantity' => 2, 'expiry_date' => '2026-03-01'],
            ['name' => 'Alphachymoral (1box=30tb)', 'unit_price' => 0.08, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 30, 'reorder_quantity' => 10, 'expiry_date' => '2026-08-01'],
            ['name' => 'Amoxicilline500mg', 'unit_price' => 0.24, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 100, 'reorder_quantity' => 20, 'expiry_date' => '2026-03-01'],
            ['name' => 'Aspirin 81mg', 'unit_price' => 0.04, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 500, 'reorder_quantity' => 100, 'expiry_date' => '2027-09-01'],
            ['name' => 'Azithro 500mg (1box=3c)', 'unit_price' => 1.50, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 100, 'reorder_quantity' => 20, 'expiry_date' => '2027-04-01'],
            ['name' => 'Cefixim 400mg (1box=10c)', 'unit_price' => 2.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 20, 'reorder_quantity' => 10, 'expiry_date' => '2027-09-01'],
            ['name' => 'Ceftriaxone 1g(1box=10)', 'unit_price' => 5.00, 'stock_quantity' => 90, 'category' => 'Bottle', 'unit' => 'box', 'dose_unit' => 'Bottle', 'total_per_box' => 10, 'reorder_quantity' => 5, 'expiry_date' => '2027-12-01'],
            ['name' => 'Cephalexine antibiotic suspension 8mg (1box=100fr)', 'unit_price' => 0.10, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 10, 'reorder_quantity' => 10, 'expiry_date' => '2025-12-01'],
            ['name' => 'Chlorhexogine 500mg (1box=1000)', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 100, 'reorder_quantity' => 5, 'expiry_date' => '2026-09-01'],
            ['name' => 'Clindamycine 300mg(1box=100tb)', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 100, 'reorder_quantity' => 5, 'expiry_date' => '2026-09-01'],
            ['name' => 'Colpotrophine 10mg (1 box=20tb)', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 10, 'reorder_quantity' => 10, 'expiry_date' => '2026-03-01'],
            ['name' => 'Cytotec 200mg', 'unit_price' => 2.20, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 20, 'reorder_quantity' => 14, 'expiry_date' => '2027-07-01'],
            ['name' => 'DPlylax 500ml (1box=dff)', 'unit_price' => 10.00, 'stock_quantity' => 90, 'category' => 'Bottle', 'unit' => 'box', 'dose_unit' => 'Bottle', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2025-12-01'],
            ['name' => 'Dexamethasone injection', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 10, 'reorder_quantity' => 3, 'expiry_date' => '2026-01-01'],
            ['name' => 'Bepantan inj', 'unit_price' => 2.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 10, 'reorder_quantity' => 2, 'expiry_date' => '2026-07-01'],
            ['name' => 'Besilin inj', 'unit_price' => 2.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 15, 'reorder_quantity' => 5, 'expiry_date' => '2025-03-01'],
            ['name' => 'Becozym inj', 'unit_price' => 3.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2025-09-01'],
            ['name' => 'Doxycycline 100mg (1box=1000s)', 'unit_price' => 0.20, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 100, 'reorder_quantity' => 20, 'expiry_date' => '2026-01-01'],
            ['name' => 'Duphastan (1box=2000)', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 2000, 'reorder_quantity' => 100, 'expiry_date' => '2027-08-01'],
            ['name' => 'Erythromycin 500', 'unit_price' => 0.32, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 50, 'reorder_quantity' => 10, 'expiry_date' => '2026-03-01'],
            ['name' => 'Flocox (1box=20tb)900mg', 'unit_price' => 1.50, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 30, 'reorder_quantity' => 3, 'expiry_date' => '2026-04-01'],
            ['name' => 'Flagyl pessery (1box=10p)', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 10, 'reorder_quantity' => 5, 'expiry_date' => '2027-01-01'],
            ['name' => 'Fluconazole 200mg', 'unit_price' => 0.50, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 50, 'reorder_quantity' => 10, 'expiry_date' => '2026-03-01'],
            ['name' => 'Glucosit (1box=10amp) 50%', 'unit_price' => 2.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 10, 'reorder_quantity' => 5, 'expiry_date' => '2026-06-01'],
            ['name' => 'Lacrose (vit C inj 1box=6amp)', 'unit_price' => 3.60, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 2, 'reorder_quantity' => 5, 'expiry_date' => '2027-09-01'],
            ['name' => 'L.P. 500(ml)', 'unit_price' => 10.00, 'stock_quantity' => 90, 'category' => 'Bottle', 'unit' => 'box', 'dose_unit' => 'Bottle', 'total_per_box' => 5, 'reorder_quantity' => 5, 'expiry_date' => '2026-12-01'],
            ['name' => 'Marekol 21', 'unit_price' => 10.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 5, 'reorder_quantity' => 2, 'expiry_date' => '2026-06-01'],
            ['name' => 'Medabon box', 'unit_price' => 20.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'Box', 'total_per_box' => 1, 'reorder_quantity' => 1, 'expiry_date' => '2026-07-01'],
            ['name' => 'Metoprolol injection', 'unit_price' => 5.00, 'stock_quantity' => 90, 'category' => 'Bottle', 'unit' => 'box', 'dose_unit' => 'Bottle', 'total_per_box' => 5, 'reorder_quantity' => 5, 'expiry_date' => '2026-01-01'],
            ['name' => 'Metronidazole 500mg(1box=1000)', 'unit_price' => 0.20, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 50, 'reorder_quantity' => 20, 'expiry_date' => '2026-09-01'],
            ['name' => 'Motillium 10mg (Suspension) box', 'unit_price' => 8.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 50, 'reorder_quantity' => 10, 'expiry_date' => '2026-04-01'],
            ['name' => 'NSS 1000ml big (box=10f)', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Bottle', 'unit' => 'box', 'dose_unit' => 'Bottle', 'total_per_box' => 10, 'reorder_quantity' => 5, 'expiry_date' => '2026-04-01'],
            ['name' => 'NS5 100ml', 'unit_price' => 2.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2026-12-01'],
            ['name' => 'Oxytocin (1box=10ml)', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 10, 'reorder_quantity' => 2, 'expiry_date' => '2026-10-01'],
            ['name' => 'Paracetamol (1box=100 1000mg)', 'unit_price' => 5.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 100, 'reorder_quantity' => 100, 'expiry_date' => '2027-05-01'],
            ['name' => 'Prednisolone', 'unit_price' => 0.12, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 40, 'reorder_quantity' => 10, 'expiry_date' => '2026-09-01'],
            ['name' => 'Preventi Inj (fertility 1/4 100un)', 'unit_price' => 3.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 15, 'reorder_quantity' => 5, 'expiry_date' => '2027-08-01'],
            ['name' => 'Proven Inj (fertility 2/g 150mg)', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Tube', 'unit' => 'box', 'dose_unit' => 'Tube', 'total_per_box' => 200, 'reorder_quantity' => 10, 'expiry_date' => '2026-08-01'],
            ['name' => 'Safanem Inj', 'unit_price' => 3.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 15, 'reorder_quantity' => 5, 'expiry_date' => '2025-08-01'],
            ['name' => 'Salbutamol 20mg', 'unit_price' => 0.20, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 20, 'reorder_quantity' => 10, 'expiry_date' => '2025-12-01'],
            ['name' => 'Sofralon inj', 'unit_price' => 0.40, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 20, 'reorder_quantity' => 20, 'expiry_date' => '2027-08-01'],
            ['name' => 'Sofialon Wash + Ovira', 'unit_price' => 3.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 10, 'reorder_quantity' => 5, 'expiry_date' => '2026-12-01'],
            ['name' => 'Suprax (1box=20tb)', 'unit_price' => 2.50, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 20, 'reorder_quantity' => 10, 'expiry_date' => '2025-12-01'],
            ['name' => 'Trinmeth Inj', 'unit_price' => 3.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 10, 'reorder_quantity' => 2, 'expiry_date' => '2026-03-01'],
            ['name' => 'Utrofemox (Inj)', 'unit_price' => 6.20, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 100, 'reorder_quantity' => 25, 'expiry_date' => '2026-08-01'],
            ['name' => 'Vitafort I inj', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 200, 'reorder_quantity' => 30, 'expiry_date' => '2026-04-01'],
            ['name' => 'VY Used', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Tube', 'unit' => 'box', 'dose_unit' => 'Tube', 'total_per_box' => 500, 'reorder_quantity' => 20, 'expiry_date' => '2026-08-01'],
            ['name' => 'Transylem (1box=50mg)', 'unit_price' => 3.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 15, 'reorder_quantity' => 5, 'expiry_date' => '2026-08-01'],
            ['name' => 'Safanem inj (1box=10amp)', 'unit_price' => 0.22, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 10, 'reorder_quantity' => 2, 'expiry_date' => '2026-06-01'],
            ['name' => 'HCT (swag)', 'unit_price' => 50.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 30, 'reorder_quantity' => 5, 'expiry_date' => '2027-02-01'],
            ['name' => 'Muningous (hon)', 'unit_price' => 20.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'Box', 'total_per_box' => 12, 'reorder_quantity' => 2, 'expiry_date' => '2026-07-01'],
            ['name' => 'HCT one', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'Box', 'total_per_box' => 18, 'reorder_quantity' => 5, 'expiry_date' => '2026-07-01'],
            ['name' => 'Primola (Sachets)', 'unit_price' => 10.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'Box', 'total_per_box' => 100, 'reorder_quantity' => 10, 'expiry_date' => '2026-01-01'],
            ['name' => 'Primola for grandil', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'Box', 'total_per_box' => 12, 'reorder_quantity' => 5, 'expiry_date' => '2026-05-01'],
            ['name' => 'Fogarsi B', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 12, 'reorder_quantity' => 5, 'expiry_date' => '2026-03-01'],
            ['name' => 'Otuflutol', 'unit_price' => 220.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 0, 'reorder_quantity' => 2, 'expiry_date' => '2026-12-01'],
            ['name' => 'LT-6 Massage Gel tube', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Tube', 'unit' => 'box', 'dose_unit' => 'Tube', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2027-04-01'],
            ['name' => 'LT-6 Massage foam stick', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2026-05-01'],
            ['name' => 'Long time Gel Tube', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Tube', 'unit' => 'box', 'dose_unit' => 'Tube', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2026-08-01'],
            ['name' => 'Plusa-2D', 'unit_price' => 7.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2026-09-01'],
            ['name' => 'Femdose (bottle)', 'unit_price' => 20.00, 'stock_quantity' => 90, 'category' => 'Bottle', 'unit' => 'box', 'dose_unit' => 'Bottle', 'total_per_box' => 10, 'reorder_quantity' => 3, 'expiry_date' => '2026-09-01'],
            ['name' => 'Crimiolog (bot)', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Bottle', 'unit' => 'box', 'dose_unit' => 'Bottle', 'total_per_box' => 5, 'reorder_quantity' => 5, 'expiry_date' => '2025-09-01'],
            ['name' => 'Cartiflexz C (Tab)', 'unit_price' => 1.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 2, 'reorder_quantity' => 2, 'expiry_date' => '2026-01-01'],
            ['name' => 'DHA C-omplex (Orange 3)', 'unit_price' => 26.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 30, 'reorder_quantity' => 5, 'expiry_date' => '2027-02-01'],
            ['name' => 'Drefuge(box)', 'unit_price' => 40.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2026-12-01'],
            ['name' => 'Carbonne', 'unit_price' => 20.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2026-04-01'],
            ['name' => 'Carbosam(Syrup)', 'unit_price' => 80.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 0, 'reorder_quantity' => 2, 'expiry_date' => '2026-02-01'],
            ['name' => 'Penil ACT', 'unit_price' => 20.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 100, 'reorder_quantity' => 5, 'expiry_date' => '2026-08-01'],
            ['name' => 'Smullium tel', 'unit_price' => 18.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 5, 'reorder_quantity' => 5, 'expiry_date' => '2025-10-01'],
            ['name' => 'Perstibse (Cowplet fil)', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2026-01-01'],
            ['name' => 'Eusi Maginesium', 'unit_price' => 15.00, 'stock_quantity' => 90, 'category' => 'Ampoul', 'unit' => 'box', 'dose_unit' => 'Ampoul', 'total_per_box' => 20, 'reorder_quantity' => 3, 'expiry_date' => '2026-04-01'],
            ['name' => 'Pragnuex (Kit)', 'unit_price' => 0.10, 'stock_quantity' => 90, 'category' => 'Tube', 'unit' => 'box', 'dose_unit' => 'Tube', 'total_per_box' => 5, 'reorder_quantity' => 2, 'expiry_date' => '2026-12-01'],
            ['name' => 'Pregnancy Mini(box)', 'unit_price' => 38.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'Box', 'total_per_box' => 50, 'reorder_quantity' => 10, 'expiry_date' => '2026-12-01'],
            ['name' => 'Pragnaxex (Box)', 'unit_price' => 20.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 20, 'reorder_quantity' => 5, 'expiry_date' => '2026-01-01'],
            ['name' => 'Pregnaxex Plus', 'unit_price' => 30.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 0, 'reorder_quantity' => 2, 'expiry_date' => '2026-01-01'],
            ['name' => 'bubfel(mix)', 'unit_price' => 2.00, 'stock_quantity' => 90, 'category' => 'Tablet', 'unit' => 'box', 'dose_unit' => 'គ្រាប់', 'total_per_box' => 10, 'reorder_quantity' => 5, 'expiry_date' => '2026-04-01'],
            ['name' => 'ITK(swag)', 'unit_price' => 50.00, 'stock_quantity' => 90, 'category' => 'Syringe', 'unit' => 'box', 'dose_unit' => 'Syringe', 'total_per_box' => 10, 'reorder_quantity' => 5, 'expiry_date' => '2026-04-01'],
        ];

        foreach ($medicines as $medicine) {
            RxMedicine::updateOrCreate(
                ['name' => $medicine['name']],
                $medicine
            );
        }
    }
}
