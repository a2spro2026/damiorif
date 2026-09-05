<?php

namespace App\Console\Commands;

use App\Models\BonAchat;
use App\Models\BonAchatLigne;
use App\Models\ReglementAchat;
use App\Models\ReglementAchatLigne;
use App\Models\StockMouvement;
use App\Support\Depots;
use App\Support\StockDepotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixBelkciriAlimentationCommand extends Command
{
    protected $signature = 'damiorif:fix-belkciri-alimentation {--dry-run : Afficher sans modifier}';

    protected $description = 'Reprend les achats Belkciri en stock initial DamioRif + transfert alimentation, puis vide les bons d\'achat';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $central = Depots::centralKey();
        $belkciri = 'belkciri';

        $allInitial = [];
        $belkciriQty = [];

        BonAchatLigne::query()
            ->with('bonAchat:id,depot')
            ->get(['id', 'bon_achat_id', 'ref', 'designation', 'qte'])
            ->each(function (BonAchatLigne $ligne) use (&$allInitial, &$belkciriQty, $belkciri) {
                $designation = trim((string) $ligne->designation);
                if ($designation === '') {
                    return;
                }

                $qty = (float) $ligne->qte;
                if ($qty <= 0) {
                    return;
                }

                $key = StockDepotService::productKey($ligne->ref, $designation);
                $ref = trim((string) $ligne->ref);
                $ref = ($ref !== '' && $ref !== '—') ? $ref : '—';

                if (! isset($allInitial[$key])) {
                    $allInitial[$key] = [
                        'ref' => $ref,
                        'designation' => $designation,
                        'qte' => 0.0,
                    ];
                }
                $allInitial[$key]['qte'] += $qty;

                $depot = $ligne->bonAchat?->depot;
                if ($depot === $belkciri) {
                    if (! isset($belkciriQty[$key])) {
                        $belkciriQty[$key] = [
                            'ref' => $ref,
                            'designation' => $designation,
                            'qte' => 0.0,
                        ];
                    }
                    $belkciriQty[$key]['qte'] += $qty;
                }
            });

        $bonsCount = BonAchat::query()->count();

        $this->info('Bons d\'achat : '.$bonsCount);
        $this->info('Lignes → stock initial DamioRif : '.count($allInitial));
        $this->info('Lignes → transfert Belkciri : '.count($belkciriQty));

        foreach ($belkciriQty as $row) {
            $this->line(sprintf(
                '  BELK %s | %s | %s',
                $row['ref'],
                $row['designation'],
                number_format($row['qte'], 3, '.', '')
            ));
        }

        if ($dry) {
            $this->warn('Dry-run : aucune modification.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($allInitial, $belkciriQty, $central, $belkciri, $bonsCount) {
            if (Schema::hasTable('reglement_achat_lignes')) {
                ReglementAchatLigne::query()->delete();
            }
            if (Schema::hasTable('reglements_achat')) {
                ReglementAchat::query()->delete();
            }
            BonAchatLigne::query()->delete();
            BonAchat::query()->delete();
            $this->info('Section Bon d\'achat vidée ('.$bonsCount.' bons).');

            $today = now()->toDateString();

            if ($allInitial !== []) {
                $entree = StockMouvement::create([
                    'date_mouvement' => $today,
                    'numero' => StockMouvement::nextNumero(),
                    'type' => 'entree',
                    'depot' => $central,
                    'depot_destination' => null,
                    'note' => 'Stock initial (reprise bons d\'achat)',
                    'user_id' => null,
                    'user_name' => 'system',
                ]);

                foreach ($allInitial as $row) {
                    $entree->lignes()->create([
                        'ref_produit' => $row['ref'] === '—' ? null : $row['ref'],
                        'designation' => $row['designation'],
                        'quantite' => round($row['qte'], 3),
                    ]);
                }
                $this->info('Stock initial DamioRif : '.$entree->numero);
            }

            if ($belkciriQty !== []) {
                $destLabel = Depots::options()[$belkciri] ?? 'Depot Belkciri';
                $transfert = StockMouvement::create([
                    'date_mouvement' => $today,
                    'numero' => StockMouvement::nextNumero(),
                    'type' => 'transfert',
                    'depot' => $central,
                    'depot_destination' => $belkciri,
                    'note' => 'Alimenter dépôt → '.$destLabel,
                    'user_id' => null,
                    'user_name' => 'system',
                ]);

                foreach ($belkciriQty as $row) {
                    $transfert->lignes()->create([
                        'ref_produit' => $row['ref'] === '—' ? null : $row['ref'],
                        'designation' => $row['designation'],
                        'quantite' => round($row['qte'], 3),
                    ]);
                }
                $this->info('Bon d\'alimentation Belkciri : '.$transfert->numero);
            }
        });

        $this->info('Terminé.');

        return self::SUCCESS;
    }
}
