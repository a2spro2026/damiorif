<?php

namespace App\Http\Controllers;

use App\Models\StockMouvement;
use App\Support\Depots;
use App\Support\ProduitReferenceService;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

        return view('stock.alimenter-depot.index', [
            'centralKey' => $central,
            'centralLabel' => Depots::options()[$central],
            'destinations' => $destinations,
            'references' => ProduitReferenceService::catalogue(),
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

        $user = $request->user();
        $destLabel = Depots::options()[$data['depot_destination']] ?? $data['depot_destination'];

        $montantTotal = 0.0;
        foreach ($data['lignes'] as $ligne) {
            $montantTotal += ((float) $ligne['qte']) * ((float) $ligne['prix_unitaire']);
        }
        $montantTotal = round($montantTotal, 2);

        DB::transaction(function () use ($data, $central, $user, $destLabel, $montantTotal) {
            // 1) Stock initial DamioRif (entrée) — même si le dépôt était vide.
            $entree = StockMouvement::create([
                'date_mouvement' => $data['date_mouvement'],
                'numero' => StockMouvement::nextNumero(),
                'type' => 'entree',
                'depot' => $central,
                'depot_destination' => null,
                'note' => 'Stock initial (alimentation → '.$destLabel.')',
                'montant' => $montantTotal,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);

            foreach ($data['lignes'] as $ligne) {
                $qte = (float) $ligne['qte'];
                $pu = (float) $ligne['prix_unitaire'];
                $entree->lignes()->create([
                    'ref_produit' => $ligne['ref'] ?? null,
                    'designation' => $ligne['designation'],
                    'quantite' => $qte,
                    'prix_unitaire' => $pu,
                    'sous_total' => round($qte * $pu, 2),
                ]);
            }

            // 2) Transfert DamioRif → dépôt régional (sortie du central).
            $transfert = StockMouvement::create([
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
                $transfert->lignes()->create([
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
