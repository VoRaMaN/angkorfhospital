<?php

namespace App\Console\Commands;

use App\Enums\SupplyTypeEnum;
use App\Models\Inventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateRxMedicinePrices extends Command
{
    protected $signature = 'inventory:update-rx-medicine-prices {--dry-run : Preview changes without updating}';

    protected $description = 'Update RX medicine inventory details (price, qty, category, expiry, etc.) from the latest price list';

    /**
     * Parse expiry date strings like "27-Sep" → "2027-09-30", "0" or empty → null.
     */
    protected function parseExpiry(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '' || $raw === '0' || str_starts_with($raw, '0-')) {
            return null;
        }

        $map = [
            'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
            'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
            'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12',
        ];

        if (preg_match('/^(\d{2})-([A-Za-z]{3})$/', $raw, $m)) {
            $year = '20'.$m[1];
            $month = $map[ucfirst(strtolower($m[2]))] ?? null;
            if ($month) {
                $lastDay = cal_days_in_month(CAL_GREGORIAN, (int) $month, (int) $year);

                return "{$year}-{$month}-{$lastDay}";
            }
        }

        return null;
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting RX Medicine Full Detail Update...');
        $this->newLine();

        // Keyed by EXACT item_name as it exists in the inventories table
        // Keys: price, qty (=original_quantity), reorder (=minimum_stock),
        //       category, uom (=total_per_box), dose_unit, unit, expiry, description
        // All existing RX medicines — keyed by EXACT item_name in the inventories table.
        // Fields: price, qty (=original_quantity & quantity), reorder (=minimum_stock),
        //         category, uom (=total_per_box), dose_unit, unit, expiry (YY-Mon), description
        $items = [
            'Acide Folique 5mg' => ['price' => 0.04,   'qty' => 3500, 'reorder' => 1000, 'category' => 'Tablet',    'uom' => 1000, 'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '28-Aug', 'description' => ''],
            'Albendazole 400mg' => ['price' => 1.50,   'qty' => 15,   'reorder' => 5,    'category' => 'Tablet',    'uom' => 10,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '28-Oct', 'description' => ''],
            'Albumin' => ['price' => 70.00,  'qty' => 5,    'reorder' => 2,    'category' => 'Bottle',    'uom' => 5,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '27-Oct', 'description' => ''],
            'Alphachymoral (1box=30tb)' => ['price' => 0.08,   'qty' => 45,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Jul', 'description' => ''],
            'Amoxicilline500mg' => ['price' => 0.24,   'qty' => 60,   'reorder' => 20,   'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Jul', 'description' => ''],
            'Aspririn 81mg' => ['price' => 0.04,   'qty' => 800,  'reorder' => 100,  'category' => 'Tablet',    'uom' => 500,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Sep', 'description' => ''],
            'Atrophyl(Vagina Gel)' => ['price' => 18.00,  'qty' => 0,    'reorder' => 2,    'category' => 'Tablet',    'uom' => 5,    'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Azitro 500mg(1box=3c)' => ['price' => 1.60,   'qty' => 70,   'reorder' => 20,   'category' => 'Tablet',    'uom' => 3,    'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '28-Aug', 'description' => ''],
            'Baby-Genius Prenatal with DHA' => ['price' => 10.00,  'qty' => 35,   'reorder' => 5,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Feb', 'description' => ''],
            'Becozym inj' => ['price' => 3.00,   'qty' => 20,   'reorder' => 5,    'category' => 'Injection', 'uom' => 20,   'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '27-Jul', 'description' => ''],
            'Bepantan inj' => ['price' => 2.00,   'qty' => 6,    'reorder' => 5,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '27-Jul', 'description' => ''],
            'Besilan inj' => ['price' => 2.00,   'qty' => 6,    'reorder' => 5,    'category' => 'Injection', 'uom' => 15,   'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '27-Mar', 'description' => ''],
            'Bromocriptine 25mg (Thai)' => ['price' => 1.00,   'qty' => 90,   'reorder' => 20,   'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '28-Sep', 'description' => ''],
            'Cefixim 400mg (1box=5tb)' => ['price' => 2.00,   'qty' => 30,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 5,    'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Nov', 'description' => ''],
            'Ceftriaxone 1g(1box=10)' => ['price' => 5.00,   'qty' => 30,   'reorder' => 5,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '27-Sep', 'description' => ''],
            'Cetrotide ( syring-injection )' => ['price' => 86.00,  'qty' => 20,   'reorder' => 5,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '26-Sep', 'description' => ''],
            'Chlorpheniramine meleate 4mg(1box=100tb)' => ['price' => 0.10,   'qty' => 0,    'reorder' => 10,   'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Cistomar' => ['price' => 20.00,  'qty' => 0,    'reorder' => 1,    'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Cistomar(Syrup)' => ['price' => 20.00,  'qty' => 4,    'reorder' => 1,    'category' => 'Bottle',    'uom' => 1,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '26-Aug', 'description' => ''],
            'Clindamycine 300mg(1box=100tb)' => ['price' => 1.00,   'qty' => 140,  'reorder' => 5,    'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Apr', 'description' => ''],
            'Colpotropine 10mg (1 box=20tb)' => ['price' => 1.00,   'qty' => 0,    'reorder' => 10,   'category' => 'Tablet',    'uom' => 20,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Coriosurge XP 5000IU' => ['price' => 35.00,  'qty' => 30,   'reorder' => 5,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '26-Jul', 'description' => ''],
            'Cytotec 200mg' => ['price' => 2.20,   'qty' => 28,   'reorder' => 14,   'category' => 'Tablet',    'uom' => 20,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Apr', 'description' => ''],
            'D5%S 500ml (1box=20fl)' => ['price' => 10.00,  'qty' => 20,   'reorder' => 5,    'category' => 'Injection', 'uom' => 20,   'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '28-Jun', 'description' => ''],
            'Dexamethasone injection' => ['price' => 1.00,   'qty' => 6,    'reorder' => 3,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '27-May', 'description' => ''],
            'DHA Complex (Omega 3)' => ['price' => 26.00,  'qty' => 11,   'reorder' => 5,    'category' => 'Tablet',    'uom' => 20,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Jan', 'description' => ''],
            'Dienogest 2mg' => ['price' => 30.00,  'qty' => 3,    'reorder' => 2,    'category' => 'Tablet',    'uom' => 10,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Jun', 'description' => ''],
            'Diphereline (1box=7amp) (Thai)' => ['price' => 70.00,  'qty' => 93,   'reorder' => 5,    'category' => 'Injection', 'uom' => 20,   'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '27-Feb', 'description' => ''],
            'Doxycycline 100mg (1box=100tb)' => ['price' => 0.20,   'qty' => 110,  'reorder' => 20,   'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Feb', 'description' => ''],
            'Duphastan(1box=20tb)' => ['price' => 1.00,   'qty' => 1500, 'reorder' => 100,  'category' => 'Tablet',    'uom' => 20,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '29-Aug', 'description' => ''],
            'Engerix B' => ['price' => 15.00,  'qty' => 0,    'reorder' => 1,    'category' => 'Vaccine',   'uom' => 1,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Erythromycin 500' => ['price' => 0.32,   'qty' => 0,    'reorder' => 10,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Etocox (1box=20tb)90mg' => ['price' => 1.50,   'qty' => 20,   'reorder' => 3,    'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Mar', 'description' => ''],
            'Exotique(Box)' => ['price' => 30.00,  'qty' => 45,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Aug', 'description' => ''],
            'Femi ACT' => ['price' => 20.00,  'qty' => 0,    'reorder' => 1,    'category' => 'Bottle',    'uom' => 1,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Fentanyl 2ml Injection' => ['price' => 10.00,  'qty' => 20,   'reorder' => 2,    'category' => 'Injection', 'uom' => 5,    'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '28-Feb', 'description' => ''],
            'Fenza(box)' => ['price' => 20.00,  'qty' => 50,   'reorder' => 5,    'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Jan', 'description' => ''],
            'Ferrobine (bottle)' => ['price' => 20.00,  'qty' => 11,   'reorder' => 3,    'category' => 'Bottle',    'uom' => 1,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '27-Mar', 'description' => ''],
            'Ferrobine (Complet tb)' => ['price' => 15.00,  'qty' => 25,   'reorder' => 5,    'category' => 'Tablet',    'uom' => 10,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Sep', 'description' => ''],
            'Flagyl pessery (1box=10p)' => ['price' => 1.00,   'qty' => 0,    'reorder' => 5,    'category' => 'Tablet',    'uom' => 10,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Fluconazole 200mg' => ['price' => 0.50,   'qty' => 32,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Mar', 'description' => ''],
            'Fluex-3D' => ['price' => 7.00,   'qty' => 40,   'reorder' => 5,    'category' => 'Tablet',    'uom' => 16,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Feb', 'description' => ''],
            'Folisurge 300IU' => ['price' => 130.00, 'qty' => 4,    'reorder' => 5,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '27-Apr', 'description' => ''],
            'Folisurge 75IU' => ['price' => 35.00,  'qty' => 0,    'reorder' => 5,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Folisurge 900IU' => ['price' => 300.00, 'qty' => 16,   'reorder' => 5,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '27-Apr', 'description' => ''],
            'Gadasil' => ['price' => 220.00, 'qty' => 5,    'reorder' => 1,    'category' => 'Vaccine',   'uom' => 1,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '27-Feb', 'description' => ''],
            'Glocuse(1box=10amp) 50%' => ['price' => 2.00,   'qty' => 9,    'reorder' => 5,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '26-Sep', 'description' => ''],
            'Gonal F (syring)300IU' => ['price' => 220.00, 'qty' => 0,    'reorder' => 2,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '27-Oct', 'description' => ''],
            'Gonal F (syring)450IU' => ['price' => 260.00, 'qty' => 0,    'reorder' => 2,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'HuCoG 5000IU (BOX)' => ['price' => 35.00,  'qty' => 0,    'reorder' => 5,    'category' => 'Injection', 'uom' => 1,    'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Ibuprofen 400mg' => ['price' => 0.20,   'qty' => 60,   'reorder' => 15,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Mar', 'description' => ''],
            'IUD(កងដាក់ស្បុន)' => ['price' => 50.00,  'qty' => 4,    'reorder' => 5,    'category' => '',          'uom' => 10,   'dose_unit' => 'Pcs',     'unit' => 'Pcs',    'expiry' => '27-Sep', 'description' => ''],
            'Jadelle(កងដែ)' => ['price' => 70.00,  'qty' => 4,    'reorder' => 5,    'category' => '',          'uom' => 5,    'dose_unit' => 'Pcs',     'unit' => 'Pcs',    'expiry' => '',       'description' => ''],
            'Ky Gel' => ['price' => 5.00,   'qty' => 7,    'reorder' => 1,    'category' => 'Tube',      'uom' => 5,    'dose_unit' => 'ទីប',     'unit' => 'ប្រអប់', 'expiry' => '26-May', 'description' => ''],
            'Larose (vit C inj 1box/6amp)' => ['price' => 3.60,   'qty' => 0,    'reorder' => 5,    'category' => 'Injection', 'uom' => 6,    'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Long time Gel Tube' => ['price' => 15.00,  'qty' => 0,    'reorder' => 1,    'category' => 'Tablet',    'uom' => 5,    'dose_unit' => 'ទីប',     'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'LR 500(ml)' => ['price' => 10.00,  'qty' => 15,   'reorder' => 5,    'category' => 'Injection', 'uom' => 20,   'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '26-Dec', 'description' => ''],
            'M2Tone' => ['price' => 15.00,  'qty' => 4,    'reorder' => 5,    'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Apr', 'description' => ''],
            'Magnispey (box)' => ['price' => 20.00,  'qty' => 10,   'reorder' => 3,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Jun', 'description' => ''],
            'Mavelon 21' => ['price' => 10.00,  'qty' => 0,    'reorder' => 2,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'បន្ទះ',   'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Medabon box' => ['price' => 20.00,  'qty' => 2,    'reorder' => 1,    'category' => 'Tablet',    'uom' => 5,    'dose_unit' => 'បន្ទះ',   'unit' => 'ប្រអប់', 'expiry' => '26-Nov', 'description' => ''],
            'Menntas HP 75IU' => ['price' => 35.00,  'qty' => 117,  'reorder' => 5,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '26-Apr', 'description' => ''],
            'Metformin 500mg' => ['price' => 0.20,   'qty' => 110,  'reorder' => 20,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '28-Mar', 'description' => ''],
            'Metronidazol 100ml injection' => ['price' => 5.00,   'qty' => 0,    'reorder' => 2,    'category' => 'Injection', 'uom' => 1,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Metronidazol 500mg(1box=100tb)' => ['price' => 0.10,   'qty' => 140,  'reorder' => 20,   'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Sep', 'description' => ''],
            'Motilium (Domperidone 10mg)' => ['price' => 0.40,   'qty' => 100,  'reorder' => 20,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Oct', 'description' => ''],
            'Neo-Vigrimazone ( insert )box' => ['price' => 7.00,   'qty' => 50,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Sep', 'description' => ''],
            'NSS 1000ml(1big box=10fl)' => ['price' => 15.00,  'qty' => 10,   'reorder' => 5,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '26-Dec', 'description' => ''],
            'NSS 100ml' => ['price' => 2.00,   'qty' => 10,   'reorder' => 3,    'category' => 'Injection', 'uom' => 20,   'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '27-May', 'description' => ''],
            'NVEL FORT  Vaginal Insert' => ['price' => 10.00,  'qty' => 14,   'reorder' => 3,    'category' => 'Tablet',    'uom' => 20,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Jan', 'description' => ''],
            'Oestro GeL' => ['price' => 15.00,  'qty' => 3,    'reorder' => 2,    'category' => 'Tube',      'uom' => 1,    'dose_unit' => 'ទីប',     'unit' => 'ប្រអប់', 'expiry' => '26-Jul', 'description' => ''],
            'Osteohelp (box)' => ['price' => 15.00,  'qty' => 3,    'reorder' => 3,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Jul', 'description' => ''],
            'Ovitrell' => ['price' => 70.00,  'qty' => 93,   'reorder' => 3,    'category' => 'Syring',    'uom' => 1,    'dose_unit' => 'syring',  'unit' => 'ប្រអប់', 'expiry' => '27-Jun', 'description' => ''],
            'Oxytocin (1box=10am)' => ['price' => 1.00,   'qty' => 6,    'reorder' => 2,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '26-Sep', 'description' => ''],
            'Paracetamol (Bottara IV 100mg)' => ['price' => 10.00,  'qty' => 15,   'reorder' => 2,    'category' => 'Injection', 'uom' => 1,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '28-Jan', 'description' => ''],
            'Physilac Mom' => ['price' => 23.00,  'qty' => 0,    'reorder' => 2,    'category' => 'Can',       'uom' => 6,    'dose_unit' => 'កំប៉ុង',   'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Ponstan 500mg (1box=250tb)' => ['price' => 0.50,   'qty' => 200,  'reorder' => 20,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Sep', 'description' => ''],
            'Prednisolone' => ['price' => 0.12,   'qty' => 80,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 60,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Oct', 'description' => ''],
            'Pregnacare' => ['price' => 20.00,  'qty' => 0,    'reorder' => 5,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => 'Vitamin'],
            'Pregnacare Max(box)' => ['price' => 38.00,  'qty' => 22,   'reorder' => 3,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Nov', 'description' => 'Vitamin'],
            'Pregnacare Plus' => ['price' => 30.00,  'qty' => 0,    'reorder' => 3,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => 'Vitamin'],
            'Pregnamar Pluse (box)' => ['price' => 30.00,  'qty' => 15,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Dec', 'description' => ''],
            'Presen 1box=30tb(pregmit)' => ['price' => 0.50,   'qty' => 60,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Feb', 'description' => ''],
            'Profertil50mg/10tb' => ['price' => 10.00,  'qty' => 500,  'reorder' => 20,   'category' => 'Tablet',    'uom' => 10,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Dec', 'description' => ''],
            'Progyluton Box' => ['price' => 15.00,  'qty' => 24,   'reorder' => 5,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'បន្ទះ',   'unit' => 'ប្រអប់', 'expiry' => '27-Nov', 'description' => ''],
            'Progynova 2mg (tablet)' => ['price' => 0.36,   'qty' => 2000, 'reorder' => 100,  'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Aug', 'description' => ''],
            'Proluton Depot inj (Thai)' => ['price' => 18.00,  'qty' => 21,   'reorder' => 10,   'category' => 'Injection', 'uom' => 1,    'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '28-Sep', 'description' => ''],
            'Propofol 50ML' => ['price' => 30.00,  'qty' => 10,   'reorder' => 5,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '26-Jun', 'description' => ''],
            'Propofol Ampoul' => ['price' => 20.00,  'qty' => 0,    'reorder' => 5,    'category' => 'Injection', 'uom' => 20,   'dose_unit' => 'អំពូល',   'unit' => 'អំពូល',  'expiry' => '27-Jun', 'description' => ''],
            'Removpain (1box=5amp)' => ['price' => 3.00,   'qty' => 10,   'reorder' => 3,    'category' => 'Injection', 'uom' => 5,    'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '26-Sep', 'description' => ''],
            'Rimona (Capsul)' => ['price' => 13.00,  'qty' => 40,   'reorder' => 5,    'category' => 'Tablet',    'uom' => 20,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Jul', 'description' => ''],
            'Rimona (Sachets)' => ['price' => 10.00,  'qty' => 1,    'reorder' => 5,    'category' => 'កញ្ចប់',    'uom' => 12,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Jun', 'description' => ''],
            'Salbutamol 20mg' => ['price' => 0.20,   'qty' => 0,    'reorder' => 10,   'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Spasfon (1box=30tb)' => ['price' => 2.50,   'qty' => 0,    'reorder' => 10,   'category' => 'Tablet',    'uom' => 30,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'SPasfon inj' => ['price' => 0.40,   'qty' => 5,    'reorder' => 2,    'category' => 'Tablet',    'uom' => 6,    'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Jul', 'description' => ''],
            'Strallium bt' => ['price' => 18.00,  'qty' => 0,    'reorder' => 1,    'category' => 'Bottle',    'uom' => 1,    'dose_unit' => 'ដប',     'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Tetanus (amp)' => ['price' => 5.00,   'qty' => 8,    'reorder' => 5,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'អំពូល',   'unit' => 'អំពូល',  'expiry' => '26-Dec', 'description' => ''],
            'Tramadol inj' => ['price' => 2.50,   'qty' => 8,    'reorder' => 2,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'អំពូល',   'unit' => 'ប្រអប់', 'expiry' => '26-Sep', 'description' => ''],
            'Tranex 500mg Injection(1box=10amp)' => ['price' => 2.00,   'qty' => 20,   'reorder' => 3,    'category' => 'Injection', 'uom' => 10,   'dose_unit' => 'អំពូល',   'unit' => 'អំពូល',  'expiry' => '27-Jun', 'description' => ''],
            'Tranex tablet' => ['price' => 0.25,   'qty' => 0,    'reorder' => 10,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'TTo Massage form tube' => ['price' => 15.00,  'qty' => 0,    'reorder' => 1,    'category' => 'Tablet',    'uom' => 5,    'dose_unit' => 'ទីប',     'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'TTo Massage Gel tube' => ['price' => 15.00,  'qty' => 0,    'reorder' => 1,    'category' => 'Tablet',    'uom' => 5,    'dose_unit' => 'ទីប',     'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Tylenol 500mg ( 1can=325tb)' => ['price' => 0.24,   'qty' => 450,  'reorder' => 50,   'category' => 'Tablet',    'uom' => 400,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '29-Jun', 'description' => 'Paracetamol'],
            'Ultra Co-Q10' => ['price' => 25.00,  'qty' => 21,   'reorder' => 10,   'category' => 'Tablet',    'uom' => 50,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '27-Jan', 'description' => ''],
            'Ultra Magnesium' => ['price' => 15.00,  'qty' => 0,    'reorder' => 2,    'category' => 'Tablet',    'uom' => 60,   'dose_unit' => 'ប្រអប់',  'unit' => 'ប្រអប់', 'expiry' => '',       'description' => ''],
            'Utrogestan 100mg' => ['price' => 0.80,   'qty' => 1686, 'reorder' => 50,   'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '26-Jun', 'description' => ''],
            'Utrogestan 200mg' => ['price' => 1.60,   'qty' => 1500, 'reorder' => 80,   'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '28-Feb', 'description' => ''],
            'V-Gel (tube)' => ['price' => 7.00,   'qty' => 6,    'reorder' => 2,    'category' => 'Tube',      'uom' => 1,    'dose_unit' => 'ទីប',     'unit' => 'ប្រអប់', 'expiry' => '26-May', 'description' => ''],
            'Vitamin C (1box=100tb)' => ['price' => 0.08,   'qty' => 700,  'reorder' => 100,  'category' => 'Tablet',    'uom' => 100,  'dose_unit' => 'គ្រាប់',  'unit' => 'ប្រអប់', 'expiry' => '27-May', 'description' => ''],
            'Yasmin box ( 1 box=21tb)' => ['price' => 15.00,  'qty' => 5,    'reorder' => 1,    'category' => 'Tablet',    'uom' => 1,    'dose_unit' => 'បន្ទះ',   'unit' => 'ប្រអប់', 'expiry' => '27-Jun', 'description' => ''],
        ];

        // New items (already created in DB from prior runs) — include full details
        $newItems = [
            ['item_name' => 'NSS 0.9%/500ml',      'price' => 10.00, 'qty' => 0,  'reorder' => 10, 'category' => 'Injection', 'uom' => 20, 'dose_unit' => 'ដប',    'unit' => 'កេស',   'expiry' => '',       'description' => ''],
            ['item_name' => 'Doliprance 500mg',     'price' => 0.30,  'qty' => 0,  'reorder' => 100, 'category' => 'Tablet',    'uom' => 16, 'dose_unit' => 'គ្រាប់', 'unit' => 'box',   'expiry' => '',       'description' => 'Paracetamol'],
            ['item_name' => 'Atrophyl FOAM',        'price' => 15.00, 'qty' => 2,  'reorder' => 2,  'category' => 'Bottle',    'uom' => 1,  'dose_unit' => 'bottle', 'unit' => 'bottle', 'expiry' => '26-Oct', 'description' => ''],
            ['item_name' => 'Calcium Corbiere',     'price' => 24.00, 'qty' => 6,  'reorder' => 5,  'category' => 'Tube',      'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '27-Aug', 'description' => ''],
            ['item_name' => 'Calcium Corbiere KID', 'price' => 10.00, 'qty' => 2,  'reorder' => 1,  'category' => 'Tube',      'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '27-Aug', 'description' => ''],
            ['item_name' => 'Cerebral',             'price' => 35.00, 'qty' => 0,  'reorder' => 1,  'category' => 'Tube',      'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '',       'description' => ''],
            ['item_name' => 'Femax',                'price' => 10.00, 'qty' => 50, 'reorder' => 5,  'category' => 'sachet',    'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '27-Sep', 'description' => ''],
            ['item_name' => 'Fercarrie inj',        'price' => 27.00, 'qty' => 1,  'reorder' => 1,  'category' => 'Bottle',    'uom' => 1,  'dose_unit' => 'bottle', 'unit' => 'bottle', 'expiry' => '26-Aug', 'description' => ''],
            ['item_name' => 'Fertigel',             'price' => 25.00, 'qty' => 15, 'reorder' => 5,  'category' => 'Tube',      'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '26-Oct', 'description' => ''],
            ['item_name' => 'Gas-X',                'price' => 0.16,  'qty' => 50, 'reorder' => 20, 'category' => 'Tablet',    'uom' => 1,  'dose_unit' => 'tablet', 'unit' => 'box',   'expiry' => '27-Nov', 'description' => ''],
            ['item_name' => 'LIAN',                 'price' => 8.50,  'qty' => 50, 'reorder' => 5,  'category' => 'sachet',    'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '27-Sep', 'description' => ''],
            ['item_name' => 'Neurozap Plus',        'price' => 15.00, 'qty' => 25, 'reorder' => 5,  'category' => 'Tablet',    'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '26-Aug', 'description' => ''],
            ['item_name' => 'Ok inj',               'price' => 4.00,  'qty' => 4,  'reorder' => 1,  'category' => 'Injection', 'uom' => 1,  'dose_unit' => 'bottle', 'unit' => 'bottle', 'expiry' => '26-Sep', 'description' => ''],
            ['item_name' => 'Tifla Fort',           'price' => 12.00, 'qty' => 30, 'reorder' => 5,  'category' => 'sachet',    'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '27-Aug', 'description' => ''],
            ['item_name' => 'Vaxigrip Tetra',       'price' => 25.00, 'qty' => 0,  'reorder' => 1,  'category' => 'Syring',    'uom' => 1,  'dose_unit' => 'syring', 'unit' => 'syring', 'expiry' => '',       'description' => ''],
            ['item_name' => 'Max-D',                'price' => 10.00, 'qty' => 50, 'reorder' => 5,  'category' => 'Tablet',    'uom' => 1,  'dose_unit' => 'box',    'unit' => 'box',   'expiry' => '27-Sep', 'description' => ''],
        ];

        $updated = 0;
        $created = 0;
        $skipped = 0;
        $changes = [];

        DB::beginTransaction();

        try {
            $allItems = array_merge(
                array_map(fn ($d, $n) => array_merge($d, ['item_name' => $n]), $items, array_keys($items)),
                array_map(fn ($d) => $d, $newItems)
            );

            foreach ($allItems as $data) {
                $itemName = $data['item_name'];
                $newPrice = $data['price'];
                $newQty = $data['qty'];
                $expiryDate = $this->parseExpiry($data['expiry']);

                $updateFields = [
                    'selling_price' => $newPrice,
                    'unit_price' => $newPrice,
                    'quantity' => $newQty,
                    'original_quantity' => $newQty,
                    'minimum_stock' => $data['reorder'],
                    'total_per_box' => $data['uom'],
                    'dose_unit' => $data['dose_unit'],
                    'unit' => $data['unit'],
                    'expiry_date' => $expiryDate,
                    'alert_days' => 180,
                ];

                if (! empty($data['category'])) {
                    $updateFields['category'] = $data['category'];
                }

                if (! empty($data['description'])) {
                    $updateFields['description'] = $data['description'];
                }

                $item = Inventory::where('type_of_supply', SupplyTypeEnum::RX_MEDICINE)
                    ->where('item_name', $itemName)
                    ->first();

                if ($item) {
                    $changes[] = [
                        'id' => $item->id,
                        'name' => $itemName,
                        'price' => '$'.number_format($newPrice, 2),
                        'qty' => $newQty,
                        'expiry' => $expiryDate ?? '—',
                        'action' => 'UPDATE',
                    ];

                    if (! $isDryRun) {
                        $item->update($updateFields);
                        $updated++;
                    }
                } else {
                    $changes[] = [
                        'id' => '(new)',
                        'name' => $itemName,
                        'price' => '$'.number_format($newPrice, 2),
                        'qty' => $newQty,
                        'expiry' => $expiryDate ?? '—',
                        'action' => 'CREATE',
                    ];

                    if (! $isDryRun) {
                        Inventory::create(array_merge($updateFields, [
                            'item_name' => $itemName,
                            'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                        ]));
                        $created++;
                    }
                }
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->warn('DRY RUN MODE — No changes were made');
            } else {
                DB::commit();
            }

            if (! empty($changes)) {
                $this->table(
                    ['ID', 'Item Name', 'Price', 'Qty', 'Expiry', 'Action'],
                    $changes
                );
            } else {
                $this->info('All items are already up to date — nothing to change.');
            }

            $this->newLine();

            if ($isDryRun) {
                $this->info('To apply changes, run: php artisan inventory:update-rx-medicine-prices');
            } else {
                $this->info("✓ Updated {$updated} items, created {$created} new items.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
