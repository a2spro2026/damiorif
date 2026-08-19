<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\StockMouvement;
use App\Support\Depots;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockMouvementController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        $query = StockMouvement::query()->with('lignes')->orderByDesc('id');
        if ($depotKey) {
            $query->where(function ($q) use ($depotKey) {
                $q->where('depot', $depotKey)->orWhere('depot_destination', $depotKey);
            });
        }

        return view('stock.mouvement.index', [
            'mouvements' => $query->get(),
            'produits' => Produit::query()->orderBy('nom_produit')->get(),
            'depots' => UserAccess::depotOptionsFor($user),
            'lockedDepot' => $depotKey,
            'nextNumero' => StockMouvement::nextNumero(),
            'types' => [
                'entree' => 'Entrée',
                'sortie' => 'Sortie',
                'transfert' => 'Transfert',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $user = auth()->user();

        DB::transaction(function () use ($data, $user) {
            $mvt = StockMouvement::create([
                'date_mouvement' => $data['date_mouvement'],
                'numero' => StockMouvement::nextNumero(),
                'type' => $data['type'],
                'depot' => $data['depot'],
                'depot_destination' => $data['type'] === 'transfert' ? $data['depot_destination'] : null,
                'note' => $data['note'] ?? null,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);

            foreach ($data['lignes'] as $ligne) {
                $produit = ! empty($ligne['produit_id'])
                    ? Produit::query()->find($ligne['produit_id'])
                    : null;

                $mvt->lignes()->create([
                    'produit_id' => $produit?->id,
                    'ref_produit' => $produit?->ref_produit ?? ($ligne['ref_produit'] ?? null),
                    'designation' => $produit?->nom_produit ?? $ligne['designation'],
                    'unite' => $produit?->unite ?? ($ligne['unite'] ?? null),
                    'quantite' => $ligne['quantite'],
                ]);
            }
        });

        return redirect()->route('stock.mouvement');
    }

    public function destroy(StockMouvement $mouvement): RedirectResponse
    {
        $mouvement->delete();

        return redirect()->route('stock.mouvement');
    }

    private function validated(Request $request): array
    {
        $user = auth()->user();
        $locked = UserAccess::depotKey($user);

        $data = $request->validate([
            'date_mouvement' => ['required', 'date'],
            'type' => ['required', 'in:entree,sortie,transfert'],
            'depot' => ['required', 'string', 'max:50'],
            'depot_destination' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:1000'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.produit_id' => ['nullable', 'integer', 'exists:produits,id'],
            'lignes.*.ref_produit' => ['nullable', 'string', 'max:100'],
            'lignes.*.designation' => ['required_without:lignes.*.produit_id', 'nullable', 'string', 'max:255'],
            'lignes.*.unite' => ['nullable', 'string', 'max:50'],
            'lignes.*.quantite' => ['required', 'numeric', 'gt:0'],
        ], [
            'lignes.required' => 'Ajoutez au moins une ligne.',
            'lignes.*.quantite.gt' => 'La quantité doit être supérieure à 0.',
        ]);

        if (! array_key_exists($data['depot'], Depots::options())) {
            abort(422, 'Dépôt invalide.');
        }

        if ($locked) {
            $data['depot'] = $locked;
        }

        if ($data['type'] === 'transfert') {
            if (empty($data['depot_destination']) || ! array_key_exists($data['depot_destination'], Depots::options())) {
                abort(422, 'Dépôt destination obligatoire pour un transfert.');
            }
            if ($data['depot_destination'] === $data['depot']) {
                abort(422, 'Le dépôt destination doit être différent.');
            }
        }

        return $data;
    }
}
