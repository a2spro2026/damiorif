<?php

namespace App\Support;

use App\Models\BonAchatLigne;
use App\Models\BonVenteLigne;
use App\Models\StockMouvement;
use Illuminate\Support\Collection;

class StockDepotService
{
    /**
     * Stock réel consolidé par produit pour un dépôt.
     *
     * @return Collection<int, array{ref: string, designation: string, qte: float}>
     */
    public static function stockForDepot(string $depotKey): Collection
    {
        $items = [];

        BonAchatLigne::query()
            ->with('bonAchat:id,depot')
            ->whereHas('bonAchat', fn ($q) => $q->where('depot', $depotKey))
            ->get(['ref', 'designation', 'qte'])
            ->each(function ($ligne) use (&$items) {
                self::applyDelta($items, $ligne->ref, $ligne->designation, (float) $ligne->qte);
            });

        BonVenteLigne::query()
            ->with('bonVente:id,depot')
            ->whereHas('bonVente', fn ($q) => $q->where('depot', $depotKey))
            ->get(['ref', 'designation', 'qte'])
            ->each(function ($ligne) use (&$items) {
                self::applyDelta($items, $ligne->ref, $ligne->designation, -1 * (float) $ligne->qte);
            });

        StockMouvement::query()
            ->with('lignes')
            ->where(function ($q) use ($depotKey) {
                $q->where('depot', $depotKey)
                    ->orWhere('depot_destination', $depotKey);
            })
            ->get()
            ->each(function (StockMouvement $mvt) use ($depotKey, &$items) {
                foreach ($mvt->lignes as $ligne) {
                    $qty = (float) $ligne->quantite;
                    $ref = $ligne->ref_produit;
                    $designation = $ligne->designation;

                    if ($mvt->type === 'entree' && $mvt->depot === $depotKey) {
                        self::applyDelta($items, $ref, $designation, $qty);
                    } elseif ($mvt->type === 'sortie' && $mvt->depot === $depotKey) {
                        self::applyDelta($items, $ref, $designation, -$qty);
                    } elseif ($mvt->type === 'transfert') {
                        if ($mvt->depot === $depotKey) {
                            self::applyDelta($items, $ref, $designation, -$qty);
                        }
                        if ($mvt->depot_destination === $depotKey) {
                            self::applyDelta($items, $ref, $designation, $qty);
                        }
                    }
                }
            });

        return collect($items)
            ->map(fn (array $row) => [
                'ref' => $row['ref'],
                'designation' => $row['designation'],
                'qte' => round($row['qte'], 3),
            ])
            ->filter(fn (array $row) => abs($row['qte']) > 0.0005)
            ->sortBy(fn (array $row) => mb_strtolower($row['ref'].' '.$row['designation']))
            ->values();
    }

    /**
     * @param  array<string, array{ref: string, designation: string, qte: float}>  $items
     */
    private static function applyDelta(array &$items, ?string $ref, ?string $designation, float $delta): void
    {
        $designation = trim((string) $designation);
        if ($designation === '') {
            return;
        }

        $key = self::productKey($ref, $designation);

        if (! isset($items[$key])) {
            $items[$key] = [
                'ref' => self::displayRef($ref),
                'designation' => $designation,
                'qte' => 0.0,
            ];
        }

        $items[$key]['qte'] += $delta;
    }

    public static function productKey(?string $ref, string $designation): string
    {
        $ref = trim((string) $ref);
        if ($ref !== '' && $ref !== '—') {
            return 'r:'.mb_strtolower($ref);
        }

        return 'd:'.mb_strtolower($designation);
    }

    private static function displayRef(?string $ref): string
    {
        $ref = trim((string) $ref);

        return ($ref !== '' && $ref !== '—') ? $ref : '—';
    }
}
