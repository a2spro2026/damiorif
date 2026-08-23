<?php

namespace App\Support;

use App\Models\BonVenteLigne;
use Illuminate\Support\Collection;

class ManquesDepotService
{
    /**
     * Manques par dépôt régional : qté vendue (période) − stock actuel.
     *
     * @return Collection<int, array{
     *   depot: string,
     *   depot_label: string,
     *   ref: string,
     *   designation: string,
     *   qte_vendue: float,
     *   qte_stock: float,
     *   manque: float
     * }>
     */
    public static function regionalManques(?string $depotKey = null, ?string $mois = null, bool $onlyWithManque = true): Collection
    {
        $depotKeys = $depotKey
            ? [$depotKey]
            : Depots::regionalKeys();

        $rows = collect();

        foreach ($depotKeys as $key) {
            if (! Depots::isRegional($key)) {
                continue;
            }

            $stockMap = self::stockMap($key);
            $ventesMap = self::ventesMap($key, $mois);
            $allKeys = collect(array_keys($stockMap))->merge(array_keys($ventesMap))->unique();

            foreach ($allKeys as $productKey) {
                $stock = $stockMap[$productKey] ?? ['ref' => '—', 'designation' => '', 'qte' => 0.0];
                $vente = $ventesMap[$productKey] ?? ['ref' => $stock['ref'], 'designation' => $stock['designation'], 'qte' => 0.0];

                $ref = $vente['ref'] !== '—' ? $vente['ref'] : $stock['ref'];
                $designation = $vente['designation'] ?: $stock['designation'];
                $qteVendue = round((float) $vente['qte'], 3);
                $qteStock = round((float) $stock['qte'], 3);
                $manque = round(max(0, $qteVendue - $qteStock), 3);

                if ($onlyWithManque && $manque <= 0.0005) {
                    continue;
                }

                $rows->push([
                    'depot' => $key,
                    'depot_label' => Depots::options()[$key] ?? $key,
                    'ref' => $ref,
                    'designation' => $designation,
                    'qte_vendue' => $qteVendue,
                    'qte_stock' => $qteStock,
                    'manque' => $manque,
                ]);
            }
        }

        return $rows
            ->sortBy(fn (array $r) => $r['depot_label'].'-'.mb_strtolower($r['ref'].' '.$r['designation']))
            ->values();
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
    private static function ventesMap(string $depotKey, ?string $mois): array
    {
        $map = [];

        $query = BonVenteLigne::query()
            ->select(['ref', 'designation', 'qte'])
            ->whereHas('bonVente', function ($q) use ($depotKey, $mois) {
                $q->where('depot', $depotKey);
                if ($mois && preg_match('/^\d{4}-\d{2}$/', $mois)) {
                    [$year, $month] = array_map('intval', explode('-', $mois));
                    $q->whereYear('date_bon', $year)->whereMonth('date_bon', $month);
                }
            });

        foreach ($query->get() as $ligne) {
            $designation = trim((string) $ligne->designation);
            if ($designation === '') {
                continue;
            }

            $key = StockDepotService::productKey($ligne->ref, $designation);

            if (! isset($map[$key])) {
                $ref = trim((string) $ligne->ref);
                $map[$key] = [
                    'ref' => ($ref !== '' && $ref !== '—') ? $ref : '—',
                    'designation' => $designation,
                    'qte' => 0.0,
                ];
            }

            $map[$key]['qte'] += (float) $ligne->qte;
        }

        return $map;
    }
}
