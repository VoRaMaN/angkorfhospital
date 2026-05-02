<?php

namespace App\Console\Commands;

use App\Enums\SupplyTypeEnum;
use App\Models\Inventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateRxMedicinePrices extends Command
{
    protected $signature = 'inventory:update-rx-medicine-prices {--dry-run : Preview changes without updating}';

    protected $description = 'Update RX medicine inventory selling prices and quantities from the latest price list';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting RX Medicine Price & Quantity Update...');
        $this->newLine();

        // Keyed by EXACT item_name as it exists in the inventories table
        // ['price' => selling_price, 'qty' => balance/stock from price list]
        $items = [
            'Acide Folique 5mg' => ['price' => 0.04,   'qty' => 3500],
            'Albendazole 400mg' => ['price' => 1.50,   'qty' => 15],
            'Albumin' => ['price' => 70.00,  'qty' => 5],
            'Alphachymoral (1box=30tb)' => ['price' => 0.08,   'qty' => 45],
            'Amoxicilline500mg' => ['price' => 0.24,   'qty' => 60],
            'Aspririn 81mg' => ['price' => 0.04,   'qty' => 800],
            'Atrophyl(Vagina Gel)' => ['price' => 18.00,  'qty' => 0],
            'Azitro 500mg(1box=3c)' => ['price' => 1.60,   'qty' => 70],
            'Baby-Genius Prenatal with DHA' => ['price' => 10.00,  'qty' => 35],
            'Becozym inj' => ['price' => 3.00,   'qty' => 20],
            'Bepantan inj' => ['price' => 2.00,   'qty' => 6],
            'Besilan inj' => ['price' => 2.00,   'qty' => 6],
            'Bromocriptine 25mg (Thai)' => ['price' => 1.00,   'qty' => 90],
            'Cefixim 400mg (1box=5tb)' => ['price' => 2.00,   'qty' => 30],
            'Ceftriaxone 1g(1box=10)' => ['price' => 5.00,   'qty' => 30],
            'Cetrotide ( syring-injection )' => ['price' => 86.00,  'qty' => 20],
            'Chlorpheniramine meleate 4mg(1box=100tb)' => ['price' => 0.10,   'qty' => 0],
            'Cistomar' => ['price' => 20.00,  'qty' => 0],
            'Cistomar(Syrup)' => ['price' => 20.00,  'qty' => 4],
            'Clindamycine 300mg(1box=100tb)' => ['price' => 1.00,   'qty' => 140],
            'Colpotropine 10mg (1 box=20tb)' => ['price' => 1.00,   'qty' => 0],
            'Coriosurge XP 5000IU' => ['price' => 35.00,  'qty' => 30],
            'Cytotec 200mg' => ['price' => 2.20,   'qty' => 28],
            'D5%S 500ml (1box=20fl)' => ['price' => 10.00,  'qty' => 20],
            'Dexamethasone injection' => ['price' => 1.00,   'qty' => 6],
            'DHA Complex (Omega 3)' => ['price' => 26.00,  'qty' => 11],
            'Dienogest 2mg' => ['price' => 30.00,  'qty' => 3],
            'Diphereline (1box=7amp) (Thai)' => ['price' => 70.00,  'qty' => 93],
            'Doxycycline 100mg (1box=100tb)' => ['price' => 0.20,   'qty' => 110],
            'Duphastan(1box=20tb)' => ['price' => 1.00,   'qty' => 1500],
            'Engerix B' => ['price' => 15.00,  'qty' => 0],
            'Erythromycin 500' => ['price' => 0.32,   'qty' => 0],
            'Etocox (1box=20tb)90mg' => ['price' => 1.50,   'qty' => 20],
            'Exotique(Box)' => ['price' => 30.00,  'qty' => 45],
            'Femi ACT' => ['price' => 20.00,  'qty' => 0],
            'Fentanyl 2ml Injection' => ['price' => 10.00,  'qty' => 20],
            'Fenza(box)' => ['price' => 20.00,  'qty' => 50],
            'Ferrobine (bottle)' => ['price' => 20.00,  'qty' => 11],
            'Ferrobine (Complet tb)' => ['price' => 15.00,  'qty' => 25],
            'Flagyl pessery (1box=10p)' => ['price' => 1.00,   'qty' => 0],
            'Fluconazole 200mg' => ['price' => 0.50,   'qty' => 32],
            'Fluex-3D' => ['price' => 7.00,   'qty' => 40],
            'Folisurge 300IU' => ['price' => 130.00, 'qty' => 4],
            'Folisurge 75IU' => ['price' => 35.00,  'qty' => 0],
            'Folisurge 900IU' => ['price' => 300.00, 'qty' => 16],
            'Gadasil' => ['price' => 220.00, 'qty' => 5],
            'Glocuse(1box=10amp) 50%' => ['price' => 2.00,   'qty' => 9],
            'Gonal F (syring)300IU' => ['price' => 220.00, 'qty' => 0],
            'Gonal F (syring)450IU' => ['price' => 260.00, 'qty' => 0],
            'HuCoG 5000IU (BOX)' => ['price' => 35.00,  'qty' => 0],
            'Ibuprofen 400mg' => ['price' => 0.20,   'qty' => 60],
            'IUD(កងដាក់ស្បុន)' => ['price' => 50.00,  'qty' => 4],
            'Jadelle(កងដែ)' => ['price' => 70.00,  'qty' => 4],
            'Ky Gel' => ['price' => 5.00,   'qty' => 7],
            'Larose (vit C inj 1box/6amp)' => ['price' => 3.60,   'qty' => 0],
            'Long time Gel Tube' => ['price' => 15.00,  'qty' => 0],
            'LR 500(ml)' => ['price' => 10.00,  'qty' => 15],
            'M2Tone' => ['price' => 15.00,  'qty' => 4],
            'Magnispey (box)' => ['price' => 20.00,  'qty' => 10],
            'Mavelon 21' => ['price' => 10.00,  'qty' => 0],
            'Medabon box' => ['price' => 20.00,  'qty' => 2],
            'Menntas HP 75IU' => ['price' => 35.00,  'qty' => 117],
            'Metformin 500mg' => ['price' => 0.20,   'qty' => 110],
            'Metronidazol 100ml injection' => ['price' => 5.00,   'qty' => 0],
            'Metronidazol 500mg(1box=100tb)' => ['price' => 0.10,   'qty' => 140],
            'Motilium (Domperidone 10mg)' => ['price' => 0.40,   'qty' => 100],
            'Neo-Vigrimazone ( insert )box' => ['price' => 7.00,   'qty' => 50],
            'NSS 1000ml(1big box=10fl)' => ['price' => 15.00,  'qty' => 10],
            'NSS 100ml' => ['price' => 2.00,   'qty' => 10],
            'NVEL FORT  Vaginal Insert' => ['price' => 10.00,  'qty' => 14],
            'Oestro GeL' => ['price' => 15.00,  'qty' => 3],
            'Osteohelp (box)' => ['price' => 15.00,  'qty' => 3],
            'Ovitrell' => ['price' => 70.00,  'qty' => 93],
            'Oxytocin (1box=10am)' => ['price' => 1.00,   'qty' => 6],
            'Paracetamol (Bottara IV 100mg)' => ['price' => 10.00,  'qty' => 15],
            'Physilac Mom' => ['price' => 23.00,  'qty' => 0],
            'Ponstan 500mg (1box=250tb)' => ['price' => 0.50,   'qty' => 200],
            'Prednisolone' => ['price' => 0.12,   'qty' => 80],
            'Pregnacare' => ['price' => 20.00,  'qty' => 0],
            'Pregnacare Max(box)' => ['price' => 38.00,  'qty' => 22],
            'Pregnacare Plus' => ['price' => 30.00,  'qty' => 0],
            'Pregnamar Pluse (box)' => ['price' => 30.00,  'qty' => 15],
            'Presen 1box=30tb(pregmit)' => ['price' => 0.50,   'qty' => 60],
            'Profertil50mg/10tb' => ['price' => 10.00,  'qty' => 500],
            'Progyluton Box' => ['price' => 15.00,  'qty' => 24],
            'Progynova 2mg (tablet)' => ['price' => 0.36,   'qty' => 2000],
            'Proluton Depot inj (Thai)' => ['price' => 18.00,  'qty' => 21],
            'Propofol 50ML' => ['price' => 30.00,  'qty' => 10],
            'Propofol Ampoul' => ['price' => 20.00,  'qty' => 0],
            'Removpain (1box=5amp)' => ['price' => 3.00,   'qty' => 10],
            'Rimona (Capsul)' => ['price' => 13.00,  'qty' => 40],
            'Rimona (Sachets)' => ['price' => 10.00,  'qty' => 1],
            'Salbutamol 20mg' => ['price' => 0.20,   'qty' => 0],
            'Spasfon (1box=30tb)' => ['price' => 2.50,   'qty' => 0],
            'SPasfon inj' => ['price' => 0.40,   'qty' => 5],
            'Strallium bt' => ['price' => 18.00,  'qty' => 0],
            'Tetanus (amp)' => ['price' => 5.00,   'qty' => 8],
            'Tramadol inj' => ['price' => 2.50,   'qty' => 8],
            'Tranex 500mg Injection(1box=10amp)' => ['price' => 2.00,   'qty' => 20],
            'Tranex tablet' => ['price' => 0.25,   'qty' => 0],
            'TTo Massage form tube' => ['price' => 15.00,  'qty' => 0],
            'TTo Massage Gel tube' => ['price' => 15.00,  'qty' => 0],
            'Tylenol 500mg ( 1can=325tb)' => ['price' => 0.24,   'qty' => 450],
            'Ultra Co-Q10' => ['price' => 25.00,  'qty' => 21],
            'Ultra Magnesium' => ['price' => 15.00,  'qty' => 0],
            'Utrogestan 100mg' => ['price' => 0.80,   'qty' => 1686],
            'Utrogestan 200mg' => ['price' => 1.60,   'qty' => 1500],
            'V-Gel (tube)' => ['price' => 7.00,   'qty' => 6],
            'Vitamin C (1box=100tb)' => ['price' => 0.08,   'qty' => 700],
            'Yasmin box ( 1 box=21tb)' => ['price' => 15.00,  'qty' => 5],
        ];

        // New items not yet in the inventory — will be created
        $newItems = [
            ['item_name' => 'NSS 0.9%/500ml',      'price' => 10.00, 'qty' => 0],
            ['item_name' => 'Doliprance 500mg',     'price' => 0.30,  'qty' => 0],
            ['item_name' => 'Atrophyl FOAM',        'price' => 15.00, 'qty' => 2],
            ['item_name' => 'Calcium Corbiere',     'price' => 24.00, 'qty' => 6],
            ['item_name' => 'Calcium Corbiere KID', 'price' => 10.00, 'qty' => 2],
            ['item_name' => 'Cerebral',             'price' => 35.00, 'qty' => 0],
            ['item_name' => 'Femax',                'price' => 10.00, 'qty' => 50],
            ['item_name' => 'Fercarrie inj',        'price' => 27.00, 'qty' => 1],
            ['item_name' => 'Fertigel',             'price' => 25.00, 'qty' => 15],
            ['item_name' => 'Gas-X',                'price' => 0.16,  'qty' => 50],
            ['item_name' => 'LIAN',                 'price' => 8.50,  'qty' => 50],
            ['item_name' => 'Neurozap Plus',        'price' => 15.00, 'qty' => 25],
            ['item_name' => 'Ok inj',               'price' => 4.00,  'qty' => 4],
            ['item_name' => 'Tifla Fort',           'price' => 12.00, 'qty' => 30],
            ['item_name' => 'Vaxigrip Tetra',       'price' => 25.00, 'qty' => 0],
            ['item_name' => 'Max-D',                'price' => 10.00, 'qty' => 50],
        ];

        $updated = 0;
        $created = 0;
        $changes = [];

        DB::beginTransaction();

        try {
            // Update existing items
            foreach ($items as $itemName => $data) {
                $item = Inventory::where('type_of_supply', SupplyTypeEnum::RX_MEDICINE)
                    ->where('item_name', $itemName)
                    ->first();

                if ($item) {
                    $oldPrice = (float) $item->selling_price;
                    $oldQty = (int) $item->quantity;
                    $newPrice = $data['price'];
                    $newQty = $data['qty'];

                    if ($oldPrice != $newPrice || $oldQty !== $newQty) {
                        $changes[] = [
                            'id' => $item->id,
                            'name' => $itemName,
                            'old_price' => $oldPrice,
                            'new_price' => $newPrice,
                            'old_qty' => $oldQty,
                            'new_qty' => $newQty,
                            'action' => 'UPDATE',
                        ];

                        if (! $isDryRun) {
                            $item->update([
                                'selling_price' => $newPrice,
                                'quantity' => $newQty,
                            ]);
                            $updated++;
                        }
                    }
                }
            }

            // Create new items
            foreach ($newItems as $data) {
                $exists = Inventory::where('type_of_supply', SupplyTypeEnum::RX_MEDICINE)
                    ->where('item_name', $data['item_name'])
                    ->exists();

                if (! $exists) {
                    $changes[] = [
                        'id' => '(new)',
                        'name' => $data['item_name'],
                        'old_price' => null,
                        'new_price' => $data['price'],
                        'old_qty' => null,
                        'new_qty' => $data['qty'],
                        'action' => 'CREATE',
                    ];

                    if (! $isDryRun) {
                        Inventory::create([
                            'item_name' => $data['item_name'],
                            'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                            'unit_price' => $data['price'],
                            'selling_price' => $data['price'],
                            'quantity' => $data['qty'],
                            'unit' => 'box',
                            'minimum_stock' => 5,
                        ]);
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
                    ['ID', 'Item Name', 'Old Price', 'New Price', 'Old Qty', 'New Qty', 'Action'],
                    array_map(fn ($c) => [
                        $c['id'],
                        $c['name'],
                        $c['old_price'] !== null ? '$'.number_format($c['old_price'], 2) : '—',
                        '$'.number_format($c['new_price'], 2),
                        $c['old_qty'] ?? '—',
                        $c['new_qty'],
                        $c['action'],
                    ], $changes)
                );
            } else {
                $this->info('All prices and quantities are already up to date — nothing to change.');
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
