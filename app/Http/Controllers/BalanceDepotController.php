<?php

namespace App\Http\Controllers;

use App\Models\StockMouvement;
use App\Support\Depots;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BalanceDepotController extends Controller
{
    public function index(Request $request): View
    {
        return view('stock.balance.index', $this->balanceData($request));
    }

    public function print(Request $request): View
    {
        return view('stock.balance.print', $this->balanceData($request));
    }

    public function printBon(StockMouvement $mouvement): View
    {
        abort_unless(
            $mouvement->type === 'transfert'
            && str_starts_with((string) $mouvement->note, 'Alimenter dépôt'),
            404
        );

        $user = auth()->user();
        $userDepot = UserAccess::depotKey($user);
        if ($userDepot && $userDepot !== Depots::centralKey() && $mouvement->depot_destination !== $userDepot) {
            abort(403);
        }

        $mouvement->load('lignes');
        $montant = (float) ($mouvement->montant ?? $mouvement->lignes->sum('sous_total'));

        return view('stock.balance.print-bon', [
            'mvt' => $mouvement,
            'montant' => $montant,
            'depotLabel' => Depots::options()[$mouvement->depot_destination] ?? $mouvement->depot_destination,
            'centralLabel' => Depots::options()[Depots::centralKey()] ?? 'DamioRif',
            'autoPrint' => request()->boolean('pdf'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function balanceData(Request $request): array
    {
        $user = $request->user();
        $userDepot = UserAccess::depotKey($user);
        $isCentral = ! UserAccess::isDepotUser($user) || $userDepot === Depots::centralKey();

        $depotOptions = $isCentral
            ? collect(Depots::options())->only(Depots::regionalKeys())->all()
            : array_intersect_key(Depots::options(), [$userDepot => true]);

        $selectedDepot = (string) $request->query('depot', '');
        if ($userDepot && $userDepot !== Depots::centralKey()) {
            $selectedDepot = $userDepot;
        } elseif ($selectedDepot !== '' && ! array_key_exists($selectedDepot, $depotOptions)) {
            $selectedDepot = '';
        }

        $mois = (string) $request->query('mois', '');
        $numero = trim((string) $request->query('numero', ''));

        $query = StockMouvement::query()
            ->with('lignes')
            ->where('type', 'transfert')
            ->where('depot', Depots::centralKey())
            ->where('note', 'like', 'Alimenter dépôt%')
            ->whereNotNull('depot_destination')
            ->orderByDesc('date_mouvement')
            ->orderByDesc('id');

        if ($selectedDepot !== '') {
            $query->where('depot_destination', $selectedDepot);
        }

        if ($mois !== '' && preg_match('/^\d{4}-\d{2}$/', $mois)) {
            $query->whereYear('date_mouvement', (int) substr($mois, 0, 4))
                ->whereMonth('date_mouvement', (int) substr($mois, 5, 2));
        }

        if ($numero !== '') {
            $query->where('numero', 'like', '%'.$numero.'%');
        }

        $depotLabels = Depots::options();
        /** @var Collection<int, array<string, mixed>> $rows */
        $rows = $query->get()->map(function (StockMouvement $mvt) use ($depotLabels) {
            $montant = (float) ($mvt->montant ?? $mvt->lignes->sum('sous_total'));

            return [
                'id' => $mvt->id,
                'date' => $mvt->date_mouvement?->format('d/m/Y'),
                'numero' => $mvt->numero,
                'depot' => $mvt->depot_destination,
                'depot_label' => $depotLabels[$mvt->depot_destination] ?? $mvt->depot_destination,
                'montant' => round($montant, 2),
                'solde' => round($montant, 2),
                'lignes' => $mvt->lignes->map(fn ($l) => [
                    'ref' => $l->ref_produit ?: '—',
                    'designation' => $l->designation,
                    'qte' => (float) $l->quantite,
                    'prix_unitaire' => (float) ($l->prix_unitaire ?? 0),
                    'sous_total' => (float) ($l->sous_total ?? (($l->quantite ?? 0) * ($l->prix_unitaire ?? 0))),
                ])->values()->all(),
            ];
        })->values();

        return [
            'rows' => $rows,
            'isCentral' => $isCentral,
            'depotOptions' => $depotOptions,
            'selectedDepot' => $selectedDepot,
            'mois' => $mois,
            'numero' => $numero,
            'depotLabel' => $selectedDepot !== ''
                ? ($depotLabels[$selectedDepot] ?? $selectedDepot)
                : null,
            'totalMontant' => round((float) $rows->sum('montant'), 2),
            'totalSolde' => round((float) $rows->sum('solde'), 2),
        ];
    }
}
