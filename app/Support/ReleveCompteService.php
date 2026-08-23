<?php

namespace App\Support;

use App\Models\BonAchat;
use App\Models\BonVente;
use App\Models\ReglementAchat;
use App\Models\ReglementVente;
use Illuminate\Support\Collection;

class ReleveCompteService
{
    /**
     * @return array{rows: Collection<int, array<string, mixed>>, totalAchats: float, totalPaye: float, totalSolde: float}
     */
    public static function fournisseurs(?string $mois, ?int $fournisseurId, ?string $depotKey = null): array
    {
        $bonsQuery = BonAchat::query()->orderBy('date_bon')->orderBy('id');
        if ($depotKey) {
            $bonsQuery->where('depot', $depotKey);
        }
        if ($fournisseurId) {
            $bonsQuery->where('fournisseur_id', $fournisseurId);
        }
        if ($mois) {
            self::applyMonthFilter($bonsQuery, 'date_bon', $mois);
        }

        $regsQuery = ReglementAchat::query()->with('lignes')->orderBy('date_reglement')->orderBy('id');
        if ($fournisseurId) {
            $regsQuery->where('fournisseur_id', $fournisseurId);
        }
        if ($mois) {
            self::applyMonthFilter($regsQuery, 'date_reglement', $mois);
        }

        $rows = collect();

        foreach ($bonsQuery->get() as $bon) {
            $montant = (float) $bon->montant;
            $paye = round($montant - (float) $bon->solde, 2);

            $rows->push([
                'operation' => 'Bon Achat',
                'date' => $bon->date_bon,
                'numero_bon' => $bon->numero_bon,
                'tiers' => $bon->nom_fournisseur,
                'debit' => $montant,
                'credit' => 0.0,
                'type' => TypesReglement::label($bon->type_reglement) ?? '—',
                'banque' => '—',
                'tire' => '—',
                'montant' => $montant,
                'paye' => $paye > 0 ? $paye : null,
                'imp' => null,
                'repo' => null,
                'devali' => null,
            ]);
        }

        foreach ($regsQuery->get() as $reg) {
            $montant = (float) $reg->montant;
            $statutCols = self::statutColumns($reg->statut, $montant);

            $rows->push([
                'operation' => 'Règlement',
                'date' => $reg->date_reglement,
                'numero_bon' => $reg->lignes->pluck('numero_bon')->filter()->unique()->implode(', ') ?: $reg->numero,
                'tiers' => $reg->nom_fournisseur,
                'debit' => 0.0,
                'credit' => $montant,
                'type' => TypesReglement::label($reg->type_reglement) ?? '—',
                'banque' => $reg->banque ?: '—',
                'tire' => $reg->nom_tire ?: '—',
                'montant' => $montant,
                'paye' => $statutCols['paye'],
                'imp' => $statutCols['imp'],
                'repo' => $statutCols['repo'],
                'devali' => $statutCols['devali'],
            ]);
        }

        $rows = $rows->sortBy(fn ($r) => ($r['date']?->format('Y-m-d') ?? '').'-'.$r['numero_bon'])->values();

        $totalAchats = round((float) $rows->sum('debit'), 2);
        $totalPaye = round((float) $rows->sum('credit'), 2);

        return [
            'rows' => $rows,
            'totalAchats' => $totalAchats,
            'totalPaye' => $totalPaye,
            'totalSolde' => round($totalAchats - $totalPaye, 2),
        ];
    }

    /**
     * @return array{rows: Collection<int, array<string, mixed>>, totalAchats: float, totalPaye: float, totalSolde: float}
     */
    public static function clients(?string $mois, ?int $clientId, ?string $depotKey = null): array
    {
        $bonsQuery = BonVente::query()->orderBy('date_bon')->orderBy('id');
        if ($depotKey) {
            $bonsQuery->where('depot', $depotKey);
        }
        if ($clientId) {
            $bonsQuery->where('client_id', $clientId);
        }
        if ($mois) {
            self::applyMonthFilter($bonsQuery, 'date_bon', $mois);
        }

        $regsQuery = ReglementVente::query()->with('lignes')->orderBy('date_reglement')->orderBy('id');
        if ($clientId) {
            $regsQuery->where('client_id', $clientId);
        }
        if ($depotKey) {
            $regsQuery->whereHas('lignes.bonVente', fn ($q) => $q->where('depot', $depotKey));
        }
        if ($mois) {
            self::applyMonthFilter($regsQuery, 'date_reglement', $mois);
        }

        $rows = collect();

        foreach ($bonsQuery->get() as $bon) {
            $montant = (float) $bon->montant;
            $paye = round($montant - (float) $bon->solde, 2);

            $rows->push([
                'operation' => 'Bon Vente',
                'date' => $bon->date_bon,
                'numero_bon' => $bon->numero_bon,
                'tiers' => $bon->nom_client,
                'debit' => $montant,
                'credit' => 0.0,
                'type' => TypesReglement::label($bon->type_reglement) ?? '—',
                'banque' => '—',
                'tire' => '—',
                'montant' => $montant,
                'paye' => $paye > 0 ? $paye : null,
                'imp' => null,
                'repo' => null,
                'devali' => null,
            ]);
        }

        foreach ($regsQuery->get() as $reg) {
            $montant = (float) $reg->montant;
            $statutCols = self::statutColumns($reg->statut, $montant);

            $rows->push([
                'operation' => 'Règlement',
                'date' => $reg->date_reglement,
                'numero_bon' => $reg->lignes->pluck('numero_bon')->filter()->unique()->implode(', ') ?: $reg->numero,
                'tiers' => $reg->nom_client,
                'debit' => 0.0,
                'credit' => $montant,
                'type' => TypesReglement::label($reg->type_reglement) ?? '—',
                'banque' => $reg->banque ?: '—',
                'tire' => $reg->nom_tire ?: '—',
                'montant' => $montant,
                'paye' => $statutCols['paye'],
                'imp' => $statutCols['imp'],
                'repo' => $statutCols['repo'],
                'devali' => $statutCols['devali'],
            ]);
        }

        $rows = $rows->sortBy(fn ($r) => ($r['date']?->format('Y-m-d') ?? '').'-'.$r['numero_bon'])->values();

        $totalAchats = round((float) $rows->sum('debit'), 2);
        $totalPaye = round((float) $rows->sum('credit'), 2);

        return [
            'rows' => $rows,
            'totalAchats' => $totalAchats,
            'totalPaye' => $totalPaye,
            'totalSolde' => round($totalAchats - $totalPaye, 2),
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function monthOptions(int $count = 24): array
    {
        $options = [];
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
     * @return array{paye: ?float, imp: ?float, repo: ?float, devali: ?float}
     */
    private static function statutColumns(?string $statut, float $montant): array
    {
        $cols = ['paye' => null, 'imp' => null, 'repo' => null, 'devali' => null];
        $key = match ($statut) {
            'paye' => 'paye',
            'imp' => 'imp',
            'reporte' => 'repo',
            'devalide' => 'devali',
            default => null,
        };

        if ($key) {
            $cols[$key] = $montant;
        }

        return $cols;
    }

    private static function applyMonthFilter($query, string $column, string $mois): void
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $mois)) {
            return;
        }

        [$year, $month] = array_map('intval', explode('-', $mois));
        $query->whereYear($column, $year)->whereMonth($column, $month);
    }
}
