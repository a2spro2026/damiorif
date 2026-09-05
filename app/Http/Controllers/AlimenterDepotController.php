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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AlimenterDepotController extends Controller
{
    public function index(): View
    {
        $this->assertCentralAccess();

        $central = Depots::centralKey();
        $destinations = collect(Depots::options())
            ->only(Depots::regionalKeys())
            ->all();

        $alimentations = StockMouvement::query()
            ->with('lignes')
            ->where('type', 'transfert')
            ->where('depot', $central)
            ->where('note', 'like', 'Alimenter dépôt%')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('stock.alimenter-depot.index', [
            'centralKey' => $central,
            'centralLabel' => Depots::options()[$central],
            'destinations' => $destinations,
            'references' => ProduitReferenceService::catalogue(),
            'stockCentral' => StockDepotService::stockMapForDepot($central),
            'alimentations' => $alimentations,
            'depotLabels' => Depots::options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertCentralAccess();

        $central = Depots::centralKey();
        $destinationKeys = Depots::regionalKeys();

        $data = $request->validate([
            'date_mouvement' => ['required', 'date'],
            'depot_destination' => ['required', 'string', Rule::in($destinationKeys)],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.ref' => ['nullable', 'string', 'max:100'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.qte' => ['required', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire' => ['required', 'numeric', 'min:0'],
        ], [
            'depot_destination.required' => 'Sélectionnez un dépôt destination.',
            'lignes.required' => 'Ajoutez au moins un article.',
            'lignes.*.designation.required' => 'La désignation est obligatoire.',
            'lignes.*.prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
        ]);

        $stockMap = StockDepotService::stockMapForDepot($central);
        $requested = [];
        foreach ($data['lignes'] as $ligne) {
            $key = StockDepotService::productKey($ligne['ref'] ?? null, $ligne['designation']);
            $requested[$key] = ($requested[$key] ?? 0.0) + (float) $ligne['qte'];
        }
        foreach ($requested as $key => $qty) {
            $available = $stockMap[$key] ?? 0.0;
            if ($available <= 0.0005) {
                throw ValidationException::withMessages([
                    'lignes' => 'Impossible d\'alimenter : article en rupture de stock DamioRif.',
                ]);
            }
            if ($qty > $available + 0.0005) {
                throw ValidationException::withMessages([
                    'lignes' => 'Quantité supérieure au stock DamioRif disponible.',
                ]);
            }
        }

        $user = $request->user();
        $destLabel = Depots::options()[$data['depot_destination']] ?? $data['depot_destination'];

        $montantTotal = 0.0;
        foreach ($data['lignes'] as $ligne) {
            $montantTotal += ((float) $ligne['qte']) * ((float) $ligne['prix_unitaire']);
        }
        $montantTotal = round($montantTotal, 2);

        DB::transaction(function () use ($data, $central, $user, $destLabel, $montantTotal) {
            $mvt = StockMouvement::create([
                'date_mouvement' => $data['date_mouvement'],
                'numero' => StockMouvement::nextNumero(),
                'type' => 'transfert',
                'depot' => $central,
                'depot_destination' => $data['depot_destination'],
                'note' => 'Alimenter dépôt → '.$destLabel,
                'montant' => $montantTotal,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);

            foreach ($data['lignes'] as $ligne) {
                $qte = (float) $ligne['qte'];
                $pu = (float) $ligne['prix_unitaire'];
                $mvt->lignes()->create([
                    'ref_produit' => $ligne['ref'] ?? null,
                    'designation' => $ligne['designation'],
                    'quantite' => $qte,
                    'prix_unitaire' => $pu,
                    'sous_total' => round($qte * $pu, 2),
                ]);
            }

            ProduitReferenceService::syncFromBonAchatLignes($data['lignes']);
        });

        return redirect()
            ->route('stock.alimenter_depot')
            ->with('success', 'Stock alimenté pour '.$destLabel.' — Total '.number_format($montantTotal, 2, ',', ' ').' MAD.');
    }

    private function assertCentralAccess(): void
    {
        if (UserAccess::isDepotUser(auth()->user())) {
            abort(403, 'Seul le dépôt central peut alimenter les dépôts.');
        }
    }
}
