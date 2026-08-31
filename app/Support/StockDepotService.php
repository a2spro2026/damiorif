<?php

namespace App\Support;

use App\Models\BonAchatLigne;
use App\Models\BonVenteLigne;
use App\Models\StockMouvement;
use Illuminate\Support\Collection;

class StockDepotService
{
    /**
     * Détail stock par produit : sorties cumulées et stock actuel.
     *
     * @return Collection<int, array{ref: string, designation: string, qte_sortie: float, stock_actuel: float}>
     */
    public static function detailForDepot(string $depotKey, ?int $excludeBonVenteId = null): Collection
    {
        $items = [];

        BonAchatLigne::query()
            ->with('bonAchat:id,depot')
            ->whereHas('bonAchat', fn ($q) => $q->where('depot', $depotKey))
            ->get(['ref', 'designation', 'qte'])
            ->each(function ($ligne) use (&$items) {
                self::applyStockDelta($items, $ligne->ref, $ligne->designation, (float) $ligne->qte, 0.0);
            });

        BonVenteLigne::query()
            ->with('bonVente:id,depot')
            ->whereHas('bonVente', function ($q) use ($depotKey, $excludeBonVenteId) {
                $q->where('depot', $depotKey);
                if ($excludeBonVenteId) {
                    $q->where('id', '!=', $excludeBonVenteId);
                }
            })
            ->get(['ref', 'designation', 'qte'])
            ->each(function ($ligne) use (&$items) {
                $qty = (float) $ligne->qte;
                self::applyStockDelta($items, $ligne->ref, $ligne->designation, -$qty, $qty);
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
                        self::applyStockDelta($items, $ref, $designation, $qty, 0.0);
                    } elseif ($mvt->type === 'sortie' && $mvt->depot === $depotKey) {
                        self::applyStockDelta($items, $ref, $designation, -$qty, $qty);
                    } elseif ($mvt->type === 'transfert') {
                        if ($mvt->depot === $depotKey) {
                            self::applyStockDelta($items, $ref, $designation, -$qty, $qty);
                        }
                        if ($mvt->depot_destination === $depotKey) {
                            self::applyStockDelta($items, $ref, $designation, $qty, 0.0);
                        }
                    }
                }
            });

        return collect($items)
            ->map(fn (array $row) => [
                'ref' => $row['ref'],
                'designation' => $row['designation'],
                'qte_sortie' => round($row['qte_sortie'], 3),
                'stock_actuel' => round($row['stock_actuel'], 3),
            ])
            ->filter(fn (array $row) => $row['qte_sortie'] > 0.0005 || abs($row['stock_actuel']) > 0.0005)
            ->sortBy(fn (array $row) => mb_strtolower($row['ref'].' '.$row['designation']))
            ->values();
    }

    /**
     * Stock réel consolidé par produit pour un dépôt.
     *
     * @return Collection<int, array{ref: string, designation: string, qte: float}>
     */
    public static function stockForDepot(string $depotKey, ?int $excludeBonVenteId = null): Collection
    {
        return self::detailForDepot($depotKey, $excludeBonVenteId)
            ->map(fn (array $row) => [
                'ref' => $row['ref'],
                'designation' => $row['designation'],
                'qte' => $row['stock_actuel'],
            ]);
    }

    /**
     * Stock disponible par clé produit pour un dépôt.
     *
     * @return array<string, float>
     */
    public static function stockMapForDepot(string $depotKey, ?int $excludeBonVenteId = null): array
    {
        $map = [];

        foreach (self::detailForDepot($depotKey, $excludeBonVenteId) as $row) {
            $ref = $row['ref'] === '—' ? null : $row['ref'];
            $map[self::productKey($ref, $row['designation'])] = (float) $row['stock_actuel'];
        }

        return $map;
    }

    /**
     * @param  array<int, array{ref?: string|null, designation: string, qte: float|string|int}>  $lignes
     * @return array<string, string>
     */
    public static function saleStockErrors(string $depotKey, array $lignes, ?int $excludeBonVenteId = null): array
    {
        if ($depotKey === '') {
            return ['depot' => 'Sélectionnez un dépôt pour valider le stock.'];
        }

        $stockMap = self::stockMapForDepot($depotKey, $excludeBonVenteId);
        $requested = [];

        foreach ($lignes as $ligne) {
            $designation = trim((string) ($ligne['designation'] ?? ''));
            if ($designation === '') {
                continue;
            }

            $key = self::productKey($ligne['ref'] ?? null, $designation);
            $requested[$key] = ($requested[$key] ?? 0.0) + (float) ($ligne['qte'] ?? 0);
        }

        $errors = [];

        foreach ($requested as $key => $qty) {
            $available = $stockMap[$key] ?? 0.0;

            if ($available <= 0.0005) {
                $errors['lignes'] = 'Impossible de vendre un article en rupture de stock (stock = 0).';

                break;
            }

            if ($qty > $available + 0.0005) {
                $errors['lignes'] = 'La quantité demandée dépasse le stock disponible.';

                break;
            }
        }

        return $errors;
    }

    /**
     * @param  array<string, array{ref: string, designation: string, stock_actuel: float, qte_sortie: float}>  $items
     */
    private static function applyStockDelta(array &$items, ?string $ref, ?string $designation, float $stockDelta, float $sortieDelta): void
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
                'stock_actuel' => 0.0,
                'qte_sortie' => 0.0,
            ];
        }

        $items[$key]['stock_actuel'] += $stockDelta;
        $items[$key]['qte_sortie'] += $sortieDelta;
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
