<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Support\Depots;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $depots = Depots::options();

        $stockByDepot = BonAchat::query()
            ->select('depot', DB::raw('COALESCE(SUM(montant), 0) as total'))
            ->whereNotNull('depot')
            ->where('depot', '!=', '')
            ->groupBy('depot')
            ->pluck('total', 'depot')
            ->map(fn ($v) => round((float) $v, 2))
            ->all();

        $user = auth()->user();
        $userDepot = \App\Support\UserAccess::depotKey($user);
        if ($userDepot) {
            $depots = array_intersect_key($depots, [$userDepot => true]);
        }

        $reglementsByDepot = $this->reglementsMontantByDepot();
        $chequesByDepot = $this->reglementsMontantByDepot('cheque');
        $traitesByDepot = $this->reglementsMontantByDepot('traite');
        $caisseByDepot = $this->caisseByDepot();

        $chargesByDepot = [];
        $depensesByDepot = [];
        $soldeClientsByDepot = [];

        foreach (array_keys($depots) as $key) {
            $caisseByDepot[$key] = $caisseByDepot[$key] ?? 0;
            $chargesByDepot[$key] = 0;
            $depensesByDepot[$key] = 0;
            $soldeClientsByDepot[$key] = 0;
            $stockByDepot[$key] = $stockByDepot[$key] ?? 0;
            $reglementsByDepot[$key] = $reglementsByDepot[$key] ?? 0;
            $chequesByDepot[$key] = $chequesByDepot[$key] ?? 0;
            $traitesByDepot[$key] = $traitesByDepot[$key] ?? 0;
        }

        $currentYear = (int) now()->year;
        $years = range($currentYear - 4, $currentYear);
        $monthLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        $ventesEvolution = [];

        foreach (array_keys($depots) as $key) {
            $ventesEvolution[$key] = [
                'mois' => array_fill(0, 12, 0),
                'annee' => array_fill(0, count($years), 0),
            ];
        }

        // Agrégation des ventes par dépôt (compatible SQLite + MySQL).
        if (Schema::hasTable('bons_vente')) {
            $driver = DB::connection()->getDriverName();
            $monthExpr = $driver === 'sqlite'
                ? "CAST(strftime('%m', date_bon) AS INTEGER)"
                : 'MONTH(date_bon)';
            $yearExpr = $driver === 'sqlite'
                ? "CAST(strftime('%Y', date_bon) AS INTEGER)"
                : 'YEAR(date_bon)';

            $moisRows = DB::table('bons_vente')
                ->whereYear('date_bon', $currentYear)
                ->whereNotNull('depot')
                ->select('depot', DB::raw("{$monthExpr} as mois"), DB::raw('COALESCE(SUM(montant), 0) as total'))
                ->groupBy('depot', DB::raw($monthExpr))
                ->get();

            foreach ($moisRows as $row) {
                $depot = $row->depot;
                $mois = ((int) $row->mois) - 1;
                if (isset($ventesEvolution[$depot]) && $mois >= 0 && $mois < 12) {
                    $ventesEvolution[$depot]['mois'][$mois] = round((float) $row->total, 2);
                }
            }

            $anneeRows = DB::table('bons_vente')
                ->whereYear('date_bon', '>=', $years[0])
                ->whereNotNull('depot')
                ->select('depot', DB::raw("{$yearExpr} as annee"), DB::raw('COALESCE(SUM(montant), 0) as total'))
                ->groupBy('depot', DB::raw($yearExpr))
                ->get();

            foreach ($anneeRows as $row) {
                $depot = $row->depot;
                $idx = array_search((int) $row->annee, $years, true);
                if ($idx !== false && isset($ventesEvolution[$depot])) {
                    $ventesEvolution[$depot]['annee'][$idx] = round((float) $row->total, 2);
                }
            }
        }

        $isDepotUser = \App\Support\UserAccess::isDepotUser($user);

        $derniersBonsAchat = $isDepotUser
            ? collect()
            : BonAchat::query()
                ->orderByDesc('date_bon')
                ->orderByDesc('id')
                ->limit(5)
                ->get(['date_bon', 'numero_bon', 'nom_fournisseur', 'montant', 'solde']);

        return view('dashboard.index', [
            'depots' => $depots,
            'stockDamiorif' => $stockByDepot['damiorif'] ?? 0,
            'stockByDepot' => $stockByDepot,
            'caisseByDepot' => $caisseByDepot,
            'reglementsByDepot' => $reglementsByDepot,
            'chequesByDepot' => $chequesByDepot,
            'traitesByDepot' => $traitesByDepot,
            'chargesByDepot' => $chargesByDepot,
            'depensesByDepot' => $depensesByDepot,
            'soldeFournisseurs' => $isDepotUser ? 0 : round((float) BonAchat::query()->sum('solde'), 2),
            'soldeClientsByDepot' => $soldeClientsByDepot,
            'ventesEvolution' => $ventesEvolution,
            'ventesMonthLabels' => $monthLabels,
            'ventesYearLabels' => array_map('strval', $years),
            'derniersBonsAchat' => $derniersBonsAchat,
            'isDepotUser' => $isDepotUser,
        ]);
    }

    /**
     * Caisse = règlements en espèces (encaissements ventes − décaissements achats), par dépôt.
     *
     * @return array<string, float>
     */
    private function caisseByDepot(): array
    {
        $encaissements = $this->reglementsVenteMontantByDepot('especes');
        $decaissements = $this->reglementsMontantByDepot('especes');

        $depots = array_unique(array_merge(array_keys($encaissements), array_keys($decaissements)));
        $result = [];

        foreach ($depots as $depot) {
            $result[$depot] = round(
                ($encaissements[$depot] ?? 0) - ($decaissements[$depot] ?? 0),
                2
            );
        }

        return $result;
    }

    /**
     * @return array<string, float>
     */
    private function reglementsVenteMontantByDepot(?string $typeReglement = null): array
    {
        if (! Schema::hasTable('reglement_vente_lignes') || ! Schema::hasTable('reglements_vente') || ! Schema::hasTable('bons_vente')) {
            return [];
        }

        $query = DB::table('reglement_vente_lignes')
            ->join('bons_vente', 'bons_vente.id', '=', 'reglement_vente_lignes.bon_vente_id')
            ->join('reglements_vente', 'reglements_vente.id', '=', 'reglement_vente_lignes.reglement_vente_id')
            ->whereNotNull('bons_vente.depot')
            ->where('bons_vente.depot', '!=', '');

        if ($typeReglement !== null) {
            $query->where('reglements_vente.type_reglement', $typeReglement);
        }

        return $query
            ->groupBy('bons_vente.depot')
            ->select('bons_vente.depot', DB::raw('COALESCE(SUM(reglement_vente_lignes.montant_applique), 0) as total'))
            ->pluck('total', 'depot')
            ->map(fn ($v) => round((float) $v, 2))
            ->all();
    }

    /**
     * @return array<string, float>
     */
    private function reglementsMontantByDepot(?string $typeReglement = null): array
    {
        if (! Schema::hasTable('reglement_achat_lignes') || ! Schema::hasTable('reglements_achat') || ! Schema::hasTable('bons_achat')) {
            return [];
        }

        $query = DB::table('reglement_achat_lignes')
            ->join('bons_achat', 'bons_achat.id', '=', 'reglement_achat_lignes.bon_achat_id')
            ->join('reglements_achat', 'reglements_achat.id', '=', 'reglement_achat_lignes.reglement_achat_id')
            ->whereNotNull('bons_achat.depot')
            ->where('bons_achat.depot', '!=', '');

        if ($typeReglement !== null) {
            $query->where('reglements_achat.type_reglement', $typeReglement);
        }

        return $query
            ->groupBy('bons_achat.depot')
            ->select('bons_achat.depot', DB::raw('COALESCE(SUM(reglement_achat_lignes.montant_applique), 0) as total'))
            ->pluck('total', 'depot')
            ->map(fn ($v) => round((float) $v, 2))
            ->all();
    }
}
