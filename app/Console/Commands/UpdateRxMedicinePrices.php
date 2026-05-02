<?php

namespace App\Console\Commands;

use App\Enums\SupplyTypeEnum;
use App\Models\Inventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateRxMedicinePrices extends Command
{
    protected $signature = 'inventory:update-rx-medicine-prices {--dry-run : Preview changes without updating}';

    protected $description = 'Update RX medicine inventory selling prices from the latest price list';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting RX Medicine Price Update...');
        $this->newLine();

        // Keyed by EXACT item_name as it exists in the inventories table
        $priceUpdates = [
            'Acide Folique 5mg' => 0.04,
            'Albendazole 400mg' => 1.50,
            'Albumin' => 70.00,
            'Alphachymoral (1box=30tb)' => 0.08,
            'Amoxicilline500mg' => 0.24,
            'Aspririn 81mg' => 0.04,
            'Atrophyl(Vagina Gel)' => 18.00,
            'Azitro 500mg(1box=3c)' => 1.60,
            'Baby-Genius Prenatal with DHA' => 10.00,
            'Becozym inj' => 3.00,
            'Bepantan inj' => 2.00,
            'Besilan inj' => 2.00,
            'Bromocriptine 25mg (Thai)' => 1.00,
            'Cefixim 400mg (1box=5tb)' => 2.00,
            'Ceftriaxone 1g(1box=10)' => 5.00,
            'Cetrotide ( syring-injection )' => 86.00,
            'Chlorpheniramine meleate 4mg(1box=100tb)' => 0.10,
            'Cistomar' => 20.00,
            'Cistomar(Syrup)' => 20.00,
            'Clindamycine 300mg(1box=100tb)' => 1.00,
            'Colpotropine 10mg (1 box=20tb)' => 1.00,
            'Coriosurge XP 5000IU' => 35.00,
            'Cytotec 200mg' => 2.20,
            'D5%S 500ml (1box=20fl)' => 10.00,
            'Dexamethasone injection' => 1.00,
            'DHA Complex (Omega 3)' => 26.00,
            'Dienogest 2mg' => 30.00,
            'Diphereline (1box=7amp) (Thai)' => 70.00,
            'Doxycycline 100mg (1box=100tb)' => 0.20,
            'Duphastan(1box=20tb)' => 1.00,
            'Engerix B' => 15.00,
            'Erythromycin 500' => 0.32,
            'Etocox (1box=20tb)90mg' => 1.50,
            'Exotique(Box)' => 30.00,
            'Femi ACT' => 20.00,
            'Fentanyl 2ml Injection' => 10.00,
            'Fenza(box)' => 20.00,
            'Ferrobine (bottle)' => 20.00,
            'Ferrobine (Complet tb)' => 15.00,
            'Flagyl pessery (1box=10p)' => 1.00,
            'Fluconazole 200mg' => 0.50,
            'Fluex-3D' => 7.00,
            'Folisurge 300IU' => 130.00,
            'Folisurge 75IU' => 35.00,
            'Folisurge 900IU' => 300.00,
            'Gadasil' => 220.00,
            'Glocuse(1box=10amp) 50%' => 2.00,
            'Gonal F (syring)300IU' => 220.00,
            'Gonal F (syring)450IU' => 260.00,
            'HuCoG 5000IU (BOX)' => 35.00,
            'Ibuprofen 400mg' => 0.20,
            'IUD(កងដាក់ស្បុន)' => 50.00,
            'Jadelle(កងដែ)' => 70.00,
            'Ky Gel' => 5.00,
            'Larose (vit C inj 1box/6amp)' => 3.60,
            'Long time Gel Tube' => 15.00,
            'LR 500(ml)' => 10.00,
            'M2Tone' => 15.00,
            'Magnispey (box)' => 20.00,
            'Mavelon 21' => 10.00,
            'Medabon box' => 20.00,
            'Menntas HP 75IU' => 35.00,
            'Metformin 500mg' => 0.20,
            'Metronidazol 100ml injection' => 5.00,
            'Metronidazol 500mg(1box=100tb)' => 0.10,
            'Motilium (Domperidone 10mg)' => 0.40,
            'Neo-Vigrimazone ( insert )box' => 7.00,
            'NSS 1000ml(1big box=10fl)' => 15.00,
            'NSS 100ml' => 2.00,
            'NVEL FORT  Vaginal Insert' => 10.00,
            'Oestro GeL' => 15.00,
            'Osteohelp (box)' => 15.00,
            'Ovitrell' => 70.00,
            'Oxytocin (1box=10am)' => 1.00,
            'Paracetamol (Bottara IV 100mg)' => 10.00,
            'Physilac Mom' => 23.00,
            'Ponstan 500mg (1box=250tb)' => 0.50,
            'Prednisolone' => 0.12,
            'Pregnacare' => 20.00,
            'Pregnacare Max(box)' => 38.00,
            'Pregnacare Plus' => 30.00,
            'Pregnamar Pluse (box)' => 30.00,
            'Presen 1box=30tb(pregmit)' => 0.50,
            'Profertil50mg/10tb' => 10.00,
            'Progyluton Box' => 15.00,
            'Progynova 2mg (tablet)' => 0.36,
            'Proluton Depot inj (Thai)' => 18.00,
            'Propofol 50ML' => 30.00,
            'Propofol Ampoul' => 20.00,
            'Removpain (1box=5amp)' => 3.00,
            'Rimona (Capsul)' => 13.00,
            'Rimona (Sachets)' => 10.00,
            'Salbutamol 20mg' => 0.20,
            'Spasfon (1box=30tb)' => 2.50,
            'SPasfon inj' => 0.40,
            'Strallium bt' => 18.00,
            'Tetanus (amp)' => 5.00,
            'Tramadol inj' => 2.50,
            'Tranex 500mg Injection(1box=10amp)' => 2.00,
            'Tranex tablet' => 0.25,
            'TTo Massage form tube' => 15.00,
            'TTo Massage Gel tube' => 15.00,
            'Tylenol 500mg ( 1can=325tb)' => 0.24,
            'Ultra Co-Q10' => 25.00,
            'Ultra Magnesium' => 15.00,
            'Utrogestan 100mg' => 0.80,
            'Utrogestan 200mg' => 1.60,
            'V-Gel (tube)' => 7.00,
            'Vitamin C (1box=100tb)' => 0.08,
            'Yasmin box ( 1 box=21tb)' => 15.00,
        ];

        // New items not yet in the inventory — will be created
        $newItems = [
            ['item_name' => 'NSS 0.9%/500ml',    'selling_price' => 10.00, 'unit_price' => 10.00],
            ['item_name' => 'Doliprance 500mg',   'selling_price' => 0.30,  'unit_price' => 0.30],
            ['item_name' => 'Atrophyl FOAM',      'selling_price' => 15.00, 'unit_price' => 15.00],
            ['item_name' => 'Calcium Corbiere',   'selling_price' => 24.00, 'unit_price' => 24.00],
            ['item_name' => 'Calcium Corbiere KID', 'selling_price' => 10.00, 'unit_price' => 10.00],
            ['item_name' => 'Cerebral',           'selling_price' => 35.00, 'unit_price' => 35.00],
            ['item_name' => 'Femax',              'selling_price' => 10.00, 'unit_price' => 10.00],
            ['item_name' => 'Fercarrie inj',      'selling_price' => 27.00, 'unit_price' => 27.00],
            ['item_name' => 'Fertigel',           'selling_price' => 25.00, 'unit_price' => 25.00],
            ['item_name' => 'Gas-X',              'selling_price' => 0.16,  'unit_price' => 0.16],
            ['item_name' => 'LIAN',               'selling_price' => 8.50,  'unit_price' => 8.50],
            ['item_name' => 'Neurozap Plus',      'selling_price' => 15.00, 'unit_price' => 15.00],
            ['item_name' => 'Ok inj',             'selling_price' => 4.00,  'unit_price' => 4.00],
            ['item_name' => 'Tifla Fort',         'selling_price' => 12.00, 'unit_price' => 12.00],
            ['item_name' => 'Vaxigrip Tetra',     'selling_price' => 25.00, 'unit_price' => 25.00],
            ['item_name' => 'Max-D',              'selling_price' => 10.00, 'unit_price' => 10.00],
        ];

        $updated = 0;
        $created = 0;
        $changes = [];

        DB::beginTransaction();

        try {
            // Update existing items
            foreach ($priceUpdates as $itemName => $newPrice) {
                $item = Inventory::where('type_of_supply', SupplyTypeEnum::RX_MEDICINE)
                    ->where('item_name', $itemName)
                    ->first();

                if ($item) {
                    $oldPrice = (float) $item->selling_price;

                    if ($oldPrice != $newPrice) {
                        $changes[] = [
                            'id' => $item->id,
                            'name' => $itemName,
                            'old_price' => $oldPrice,
                            'new_price' => $newPrice,
                            'action' => 'UPDATE',
                        ];

                        if (! $isDryRun) {
                            $item->update(['selling_price' => $newPrice]);
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
                        'new_price' => $data['selling_price'],
                        'action' => 'CREATE',
                    ];

                    if (! $isDryRun) {
                        Inventory::create([
                            'item_name' => $data['item_name'],
                            'type_of_supply' => SupplyTypeEnum::RX_MEDICINE,
                            'unit_price' => $data['unit_price'],
                            'selling_price' => $data['selling_price'],
                            'quantity' => 0,
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
                    ['ID', 'Item Name', 'Old Price', 'New Price', 'Action'],
                    array_map(fn ($c) => [
                        $c['id'],
                        $c['name'],
                        $c['old_price'] !== null ? '$'.number_format($c['old_price'], 2) : '—',
                        '$'.number_format($c['new_price'], 2),
                        $c['action'],
                    ], $changes)
                );
            } else {
                $this->info('All prices are already up to date — nothing to change.');
            }

            $this->newLine();

            if ($isDryRun) {
                $this->info('To apply changes, run: php artisan inventory:update-rx-medicine-prices');
            } else {
                $this->info("✓ Updated {$updated} prices, created {$created} new items.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
