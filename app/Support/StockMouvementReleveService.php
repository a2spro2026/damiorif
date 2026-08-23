<?php

namespace App\Support;

use App\Models\BonAchatLigne;
use App\Models\BonVenteLigne;
use App\Models\StockMouvement;
use Illuminate\Support\Collection;

class StockMouvementReleveService
{
    /**
     * Relevé par produit pour un dépôt : entrées, ventes/sorties (période), stock actuel.
     *
     * @return Collection<int, array{
     *   ref: string,
     *   designation: string,
     *   qte_entree: float,
     *   qte_vendue: float,
     *   qte_stock: float
     * }>
     */
    public static function releveForDepot(string $depotKey, ?string $mois = null): Collection
    {
        $entreesMap = self::entreesMap($depotKey, $mois);
        $sortiesMap = self::sortiesMap($depotKey, $mois);
        $stockMap = self::stockMap($depotKey);

        $allKeys = collect(array_keys($entreesMap))
            ->merge(array_keys($sortiesMap))
            ->merge(array_keys($stockMap))
            ->unique();

        $rows = collect();

        foreach ($allKeys as $productKey) {
            $entree = $entreesMap[$productKey] ?? ['ref' => '—', 'designation' => '', 'qte' => 0.0];
            $sortie = $sortiesMap[$productKey] ?? ['ref' => '—', 'designation' => '', 'qte' => 0.0];
            $stock = $stockMap[$productKey] ?? ['ref' => '—', 'designation' => '', 'qte' => 0.0];

            $ref = self::pickRef($entree['ref'], $sortie['ref'], $stock['ref']);
            $designation = $entree['designation'] ?: ($sortie['designation'] ?: $stock['designation']);

            if ($designation === '') {
                continue;
            }

            $qteEntree = round((float) $entree['qte'], 3);
            $qteVendue = round((float) $sortie['qte'], 3);
            $qteStock = round((float) $stock['qte'], 3);

            if ($qteEntree <= 0.0005 && $qteVendue <= 0.0005 && abs($qteStock) <= 0.0005) {
                continue;
            }

            $rows->push([
                'ref' => $ref,
                'designation' => $designation,
                'qte_entree' => $qteEntree,
                'qte_vendue' => $qteVendue,
                'qte_stock' => $qteStock,
            ]);
        }

        return $rows
            ->sortBy(fn (array $r) => mb_strtolower($r['ref'].' '.$r['designation']))
            ->values();
    }

    /**
     * Ventes mensuelles (bons vente) + stock actuel pour un dépôt et une année.
     *
     * @return Collection<int, array{
     *   ref: string,
     *   designation: string,
     *   ventes: array<int, float>,
     *   qte_stock: float
     * }>
     */
    public static function ventesMensuellesForDepot(string $depotKey, int $year): Collection
    {
        $stockMap = self::stockMap($depotKey);
        $monthlyMaps = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlyMaps[$month] = self::ventesBonVenteMap(
                $depotKey,
                sprintf('%04d-%02d', $year, $month)
            );
        }

        $allKeys = collect(array_keys($stockMap));
        for ($month = 1; $month <= 12; $month++) {
            $allKeys = $allKeys->merge(array_keys($monthlyMaps[$month]));
        }

        $rows = collect();

        foreach ($allKeys->unique() as $productKey) {
            $stock = $stockMap[$productKey] ?? ['ref' => '—', 'designation' => '', 'qte' => 0.0];
            $ventes = [];
            $hasVente = false;

            for ($month = 1; $month <= 12; $month++) {
                $qty = round((float) ($monthlyMaps[$month][$productKey]['qte'] ?? 0), 3);
                $ventes[$month] = $qty;
                if ($qty > 0.0005) {
                    $hasVente = true;
                }
            }

            $qteStock = round((float) $stock['qte'], 3);

            if (! $hasVente && abs($qteStock) <= 0.0005) {
                continue;
            }

            $designation = $stock['designation'];
            if ($designation === '') {
                foreach ($monthlyMaps as $map) {
                    if (! empty($map[$productKey]['designation'])) {
                        $designation = $map[$productKey]['designation'];
                        break;
                    }
                }
            }

            if ($designation === '') {
                continue;
            }

            $ref = $stock['ref'];
            if ($ref === '—') {
                foreach ($monthlyMaps as $map) {
                    if (($map[$productKey]['ref'] ?? '—') !== '—') {
                        $ref = $map[$productKey]['ref'];
                        break;
                    }
                }
            }

            $rows->push([
                'ref' => $ref,
                'designation' => $designation,
                'ventes' => $ventes,
                'qte_stock' => $qteStock,
            ]);
        }

        return $rows
            ->sortBy(fn (array $r) => mb_strtolower($r['ref'].' '.$r['designation']))
            ->values();
    }

    /**
     * @return array<int, string>
     */
    public static function monthShortLabels(): array
    {
        return [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Aoû',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc',
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function monthOptions(int $count = 24): array
    {
        $options = [['value' => '', 'label' => 'Toutes les périodes']];
        $date = now()->startOfMonth();

        for ($i = 0; $i < $count; $i++) {
            $value = $date->format('Y-m');
            $options[] = [
                'value' => $value,
                'label' => ucfirst($date->translatedFormat('F Y')),
            ];
            $date = $date->subMonth();
        }

        return $options;
    }

    /**
     * @return array<string, array{ref: string, designation: string, qte: float}>
     */
    private static function stockMap(string $depotKey): array
    {
        $map = [];

        foreach (StockDepotService::stockForDepot($depotKey) as $row) {
            $key = StockDepotService::productKey(
                $row['ref'] === '—' ? null : $row['ref'],
                $row['designation']
            );
            $map[$key] = $row;
        }

        return $map;
    }

    /**
     * @return array<string, array{ref: string, designation: string, qte: float}>
     */
    private static function entreesMap(string $depotKey, ?string $mois): array
    {
        $map = [];

        BonAchatLigne::query()
            ->select(['ref', 'designation', 'qte'])
            ->whereHas('bonAchat', function ($q) use ($depotKey, $mois) {
                $q->where('depot', $depotKey);
                self::applyMonthFilter($q, 'date_bon', $mois);
            })
            ->get()
            ->each(function ($ligne) use (&$map) {
                self::addQty($map, $ligne->ref, $ligne->designation, (float) $ligne->qte);
            });

        StockMouvement::query()
            ->with('lignes')
            ->where(function ($q) use ($depotKey) {
                $q->where(function ($q2) use ($depotKey) {
                    $q2->where('type', 'entree')->where('depot', $depotKey);
                })->orWhere(function ($q2) use ($depotKey) {
                    $q2->where('type', 'transfert')->where('depot_destination', $depotKey);
                });
            })
            ->when($mois, fn ($q) => self::applyMonthFilter($q, 'date_mouvement', $mois))
            ->get()
            ->each(function (StockMouvement $mvt) use (&$map) {
                foreach ($mvt->lignes as $ligne) {
                    self::addQty($map, $ligne->ref_produit, $ligne->designation, (float) $ligne->quantite);
                }
            });

        return $map;
    }

    /**
     * @return array<string, array{ref: string, designation: string, qte: float}>
     */
    private static function sortiesMap(string $depotKey, ?string $mois): array
    {
        $map = [];

        BonVenteLigne::query()
            ->select(['ref', 'designation', 'qte'])
            ->whereHas('bonVente', function ($q) use ($depotKey, $mois) {
                $q->where('depot', $depotKey);
                self::applyMonthFilter($q, 'date_bon', $mois);
            })
            ->get()
            ->each(function ($ligne) use (&$map) {
                self::addQty($map, $ligne->ref, $ligne->designation, (float) $ligne->qte);
            });

        StockMouvement::query()
            ->with('lignes')
            ->where(function ($q) use ($depotKey) {
                $q->where(function ($q2) use ($depotKey) {
                    $q2->where('type', 'sortie')->where('depot', $depotKey);
                })->orWhere(function ($q2) use ($depotKey) {
                    $q2->where('type', 'transfert')->where('depot', $depotKey);
                });
            })
            ->when($mois, fn ($q) => self::applyMonthFilter($q, 'date_mouvement', $mois))
            ->get()
            ->each(function (StockMouvement $mvt) use (&$map) {
                foreach ($mvt->lignes as $ligne) {
                    self::addQty($map, $ligne->ref_produit, $ligne->designation, (float) $ligne->quantite);
                }
            });

        return $map;
    }

    /**
     * @return array<string, array{ref: string, designation: string, qte: float}>
     */
    private static function ventesBonVenteMap(string $depotKey, string $mois): array
    {
        $map = [];

        BonVenteLigne::query()
            ->select(['ref', 'designation', 'qte'])
            ->whereHas('bonVente', function ($q) use ($depotKey, $mois) {
                $q->where('depot', $depotKey);
                self::applyMonthFilter($q, 'date_bon', $mois);
            })
            ->get()
            ->each(function ($ligne) use (&$map) {
                self::addQty($map, $ligne->ref, $ligne->designation, (float) $ligne->qte);
            });

        return $map;
    }

    /**
     * @param  array<string, array{ref: string, designation: string, qte: float}>  $map
     */
    private static function addQty(array &$map, ?string $ref, ?string $designation, float $qty): void
    {
        $designation = trim((string) $designation);
        if ($designation === '' || $qty <= 0) {
            return;
        }

        $key = StockDepotService::productKey($ref, $designation);

        if (! isset($map[$key])) {
            $ref = trim((string) $ref);
            $map[$key] = [
                'ref' => ($ref !== '' && $ref !== '—') ? $ref : '—',
                'designation' => $designation,
                'qte' => 0.0,
            ];
        }

        $map[$key]['qte'] += $qty;
    }

    private static function pickRef(string ...$refs): string
    {
        foreach ($refs as $ref) {
            if ($ref !== '—' && trim($ref) !== '') {
                return $ref;
            }
        }

        return '—';
    }

    private static function applyMonthFilter($query, string $column, ?string $mois): void
    {
        if (! $mois || ! preg_match('/^\d{4}-\d{2}$/', $mois)) {
            return;
        }

        [$year, $month] = array_map('intval', explode('-', $mois));
        $query->whereYear($column, $year)->whereMonth($column, $month);
    }
}
