<?php

namespace App\Http\Controllers;

use App\Models\StockMouvement;
use App\Support\Depots;
use App\Support\ProduitReferenceService;
use App\Support\StockDepotService;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RetourDepotController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $depotKey = UserAccess::depotKey($user);

        // Création : dépôts régionaux uniquement. Central : consultation.
        $canCreate = $depotKey !== null && Depots::isRegional($depotKey);

        $filterDepots = $canCreate
            ? [$depotKey => Depots::options()[$depotKey] ?? $depotKey]
            : collect(Depots::options())->only(Depots::regionalKeys())->all();

        $mois = (string) $request->query('mois', '');
        $filterDepot = (string) $request->query('depot', '');
        if ($canCreate) {
            $filterDepot = $depotKey;
        } elseif ($filterDepot !== '' && ! array_key_exists($filterDepot, $filterDepots)) {
            $filterDepot = '';
        }

        $query = StockMouvement::query()
            ->with('lignes')
            ->where('type', 'transfert')
            ->where('depot_destination', Depots::retourKey())
            ->where('numero', 'like', 'RT-%')
            ->orderByDesc('id');

        if ($filterDepot !== '') {
            $query->where('depot', $filterDepot);
        }

        if ($mois !== '' && preg_match('/^\d{4}-\d{2}$/', $mois)) {
            $query->whereYear('date_mouvement', (int) substr($mois, 0, 4))
                ->whereMonth('date_mouvement', (int) substr($mois, 5, 2));
        }

        $retours = $query->limit(200)->get();

        // Flatten to one row per ligne for display (Date, N°, Depot, Ref, Des, Qte, Remarque)
        $rows = [];
        foreach ($retours as $mvt) {
            $remarque = $mvt->note ?? '';
            if (str_contains($remarque, ' — ')) {
                $remarque = trim(explode(' — ', $remarque, 2)[1] ?? $remarque);
            }
            $src = Depots::options()[$mvt->depot] ?? $mvt->depot;
            foreach ($mvt->lignes as $ligne) {
                $rows[] = [
                    'date' => $mvt->date_mouvement?->format('d/m/Y'),
                    'date_raw' => $mvt->date_mouvement?->format('Y-m-d'),
                    'numero' => $mvt->numero,
                    'depot_key' => $mvt->depot,
                    'depot' => $src,
                    'ref' => $ligne->ref_produit ?: '—',
                    'designation' => $ligne->designation,
                    'qte' => (float) $ligne->quantite,
                    'remarque' => $remarque ?: '—',
                ];
            }
        }

        return view('stock.retour.index', [
            'canCreate' => $canCreate,
            'depotKey' => $depotKey,
            'depotLabel' => $depotKey ? (Depots::options()[$depotKey] ?? $depotKey) : null,
            'nextNumero' => StockMouvement::nextRetourNumero(),
            'references' => ProduitReferenceService::catalogue(),
            'stockDepot' => $canCreate ? StockDepotService::stockMapForDepot($depotKey) : [],
            'rows' => $rows,
            'filterDepots' => $filterDepots,
            'filterMois' => $mois,
            'filterDepot' => $filterDepot,
            'moisOptions' => $this->moisOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $depotKey = UserAccess::depotKey($user);

        if (! $depotKey || ! Depots::isRegional($depotKey)) {
            abort(403, 'Seul un dépôt régional peut enregistrer un retour.');
        }

        $data = $request->validate([
            'date_mouvement' => ['required', 'date'],
            'ref' => ['nullable', 'string', 'max:100'],
            'designation' => ['required', 'string', 'max:255'],
            'qte' => ['required', 'numeric', 'min:0.01'],
            'remarque' => ['required', 'string', 'max:1000'],
        ], [
            'designation.required' => 'La désignation est obligatoire.',
            'qte.required' => 'La quantité est obligatoire.',
            'remarque.required' => 'Indiquez une remarque pour justifier le retour.',
        ]);

        $lignes = [[
            'ref' => $data['ref'] ?? null,
            'designation' => $data['designation'],
            'qte' => $data['qte'],
        ]];

        $stockMap = StockDepotService::stockMapForDepot($depotKey);
        $key = StockDepotService::productKey($lignes[0]['ref'] ?? null, $lignes[0]['designation']);
        $qty = (float) $lignes[0]['qte'];
        $available = $stockMap[$key] ?? 0.0;

        if ($available <= 0.0005) {
            throw ValidationException::withMessages([
                'qte' => 'Impossible de retourner un article en rupture de stock.',
            ]);
        }
        if ($qty > $available + 0.0005) {
            throw ValidationException::withMessages([
                'qte' => 'Quantité supérieure au stock disponible du dépôt.',
            ]);
        }

        $remarque = trim($data['remarque']);
        $depotLabel = Depots::options()[$depotKey] ?? $depotKey;

        DB::transaction(function () use ($data, $depotKey, $user, $remarque, $depotLabel, $lignes) {
            $mvt = StockMouvement::create([
                'date_mouvement' => $data['date_mouvement'],
                'numero' => StockMouvement::nextRetourNumero(),
                'type' => 'transfert',
                'depot' => $depotKey,
                'depot_destination' => Depots::retourKey(),
                'note' => 'Retour '.$depotLabel.' — '.$remarque,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);

            $mvt->lignes()->create([
                'ref_produit' => $lignes[0]['ref'] ?? null,
                'designation' => $lignes[0]['designation'],
                'quantite' => $lignes[0]['qte'],
            ]);

            ProduitReferenceService::syncFromBonAchatLignes($lignes);
        });

        return redirect()
            ->route('stock.retour')
            ->with('success', 'Retour enregistré vers le Dépôt Retour.');
    }

    /**
     * @return array<string, string>
     */
    private function moisOptions(): array
    {
        $moisFr = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        $options = ['' => 'Tous les mois'];
        $cursor = now()->startOfMonth();
        for ($i = 0; $i < 24; $i++) {
            $key = $cursor->format('Y-m');
            $options[$key] = ($moisFr[(int) $cursor->format('n')] ?? $cursor->format('m')).' '.$cursor->format('Y');
            $cursor->subMonth();
        }

        return $options;
    }
}
