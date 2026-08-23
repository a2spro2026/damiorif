<?php

namespace App\Http\Controllers;

use App\Models\BonCommandeDepot;
use App\Models\StockMouvement;
use App\Support\Depots;
use App\Support\StockNotifications;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BonCommandeDepotController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $depotKey = UserAccess::depotKey($user);
        $isCentral = $depotKey === null;

        $query = BonCommandeDepot::query()->with('lignes')->orderByDesc('id');
        if ($depotKey) {
            $query->where('depot_demandeur', $depotKey);
        } else {
            $query->whereIn('depot_demandeur', Depots::regionalKeys());
        }

        $commandes = $query->get();

        $notifications = $isCentral
            ? $commandes->where('statut', 'envoye')->values()
            : collect();

        return view('stock.commande-depot.index', [
            'commandes' => $commandes,
            'depots' => Depots::options(),
            'regionalDepots' => array_intersect_key(Depots::options(), array_flip(Depots::regionalKeys())),
            'lockedDepot' => $depotKey,
            'isCentral' => $isCentral,
            'nextNumero' => BonCommandeDepot::nextNumero(),
            'nextNumeroBonCharge' => BonCommandeDepot::nextNumeroBonCharge(),
            'statuts' => self::statutLabels(),
            'notifications' => $notifications,
            'pendingCount' => StockNotifications::pendingCommandes($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $user = $request->user();
        $envoyer = $request->boolean('envoyer');

        DB::transaction(function () use ($data, $user, $envoyer) {
            $bon = BonCommandeDepot::create([
                'date_commande' => $data['date_commande'],
                'numero' => BonCommandeDepot::nextNumero(),
                'depot_demandeur' => $data['depot_demandeur'],
                'depot_fournisseur' => Depots::centralKey(),
                'statut' => $envoyer ? 'envoye' : 'brouillon',
                'note' => $data['note'] ?? null,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);

            $this->syncLignes($bon, $data['lignes']);
        });

        $message = $envoyer
            ? 'Bon envoyé au Dépôt DamioRif.'
            : 'Bon enregistré.';

        return redirect()->route('stock.commande_depot')->with('success', $message);
    }

    public function update(Request $request, BonCommandeDepot $commande): RedirectResponse
    {
        $this->authorizeBon($request, $commande);
        abort_unless($commande->isEditable(), 422);

        $data = $this->validated($request, $commande);
        $envoyer = $request->boolean('envoyer');

        DB::transaction(function () use ($data, $commande, $envoyer) {
            $commande->update([
                'date_commande' => $data['date_commande'],
                'depot_demandeur' => $data['depot_demandeur'],
                'note' => $data['note'] ?? null,
                'statut' => $envoyer ? 'envoye' : 'brouillon',
            ]);
            $commande->lignes()->delete();
            $this->syncLignes($commande, $data['lignes']);
        });

        return redirect()->route('stock.commande_depot')->with(
            'success',
            $envoyer ? 'Bon envoyé au Dépôt DamioRif.' : 'Bon mis à jour.'
        );
    }

    public function destroy(Request $request, BonCommandeDepot $commande): RedirectResponse
    {
        $this->authorizeBon($request, $commande);
        abort_unless($commande->isEditable(), 422);
        $commande->delete();

        return redirect()->route('stock.commande_depot')->with('success', 'Bon supprimé.');
    }

    public function envoyer(Request $request, BonCommandeDepot $commande): RedirectResponse
    {
        $this->authorizeBon($request, $commande);
        abort_unless($commande->statut === 'brouillon', 422);
        abort_if($commande->lignes()->count() === 0, 422);

        $commande->update(['statut' => 'envoye']);

        return redirect()->route('stock.commande_depot')->with('success', 'Bon envoyé au Dépôt DamioRif.');
    }

    public function convertir(Request $request, BonCommandeDepot $commande): RedirectResponse
    {
        abort_unless(UserAccess::depotKey($request->user()) === null, 403);
        abort_unless(in_array($commande->statut, ['envoye', 'converti'], true), 422);

        $data = $request->validate([
            'date_bon_charge' => ['required', 'date'],
        ]);

        $commande->update([
            'numero_bon_charge' => $commande->numero_bon_charge ?? BonCommandeDepot::nextNumeroBonCharge(),
            'date_bon_charge' => $data['date_bon_charge'],
            'statut' => 'converti',
        ]);

        return redirect()->route('stock.commande_depot')->with('success', 'Bon de charge enregistré.');
    }

    public function suspendre(Request $request, BonCommandeDepot $commande): RedirectResponse
    {
        abort_unless(UserAccess::depotKey($request->user()) === null, 403);
        abort_unless(in_array($commande->statut, ['envoye', 'converti', 'brouillon'], true), 422);

        $commande->update(['statut' => 'suspendu']);

        return redirect()->route('stock.commande_depot')->with('success', 'Commande suspendue.');
    }

    public function expedier(Request $request, BonCommandeDepot $commande): RedirectResponse
    {
        abort_unless(UserAccess::depotKey($request->user()) === null, 403);
        abort_unless(in_array($commande->statut, ['envoye', 'converti'], true), 422);

        DB::transaction(function () use ($commande, $request) {
            $user = $request->user();

            $mvt = StockMouvement::create([
                'date_mouvement' => now()->toDateString(),
                'numero' => StockMouvement::nextNumero(),
                'type' => 'transfert',
                'depot' => Depots::centralKey(),
                'depot_destination' => $commande->depot_demandeur,
                'note' => 'Expédition '.$commande->numero,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);

            foreach ($commande->lignes as $ligne) {
                $qte = (float) $ligne->qte_demandee;
                $mvt->lignes()->create([
                    'ref_produit' => $ligne->ref,
                    'designation' => $ligne->designation,
                    'quantite' => $qte,
                ]);
                $ligne->update(['qte_expediee' => $qte]);
            }

            $commande->update([
                'statut' => 'expedie',
                'stock_mouvement_id' => $mvt->id,
            ]);
        });

        return redirect()->route('stock.commande_depot')->with('success', 'Commande expédiée — transfert créé.');
    }

    public function print(BonCommandeDepot $commande): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);
        if ($depotKey && $commande->depot_demandeur !== $depotKey) {
            abort(403);
        }

        $commande->load('lignes');

        return view('stock.commande-depot.print', [
            'commande' => $commande,
            'depots' => Depots::options(),
            'statuts' => self::statutLabels(),
        ]);
    }

    private function validated(Request $request, ?BonCommandeDepot $existing = null): array
    {
        $user = $request->user();
        $locked = UserAccess::depotKey($user);
        $regionalKeys = Depots::regionalKeys();

        $data = $request->validate([
            'date_commande' => ['required', 'date'],
            'depot_demandeur' => ['required', 'string', Rule::in($regionalKeys)],
            'note' => ['nullable', 'string', 'max:1000'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.ref' => ['nullable', 'string', 'max:100'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.qte_demandee' => ['required', 'numeric', 'gt:0'],
        ], [
            'lignes.required' => 'Ajoutez au moins une ligne.',
            'lignes.*.designation.required' => 'La désignation est obligatoire.',
        ]);

        if ($locked) {
            $data['depot_demandeur'] = $locked;
        }

        return $data;
    }

    private function syncLignes(BonCommandeDepot $bon, array $lignes): void
    {
        foreach ($lignes as $ligne) {
            $bon->lignes()->create([
                'ref' => $ligne['ref'] ?? null,
                'designation' => $ligne['designation'],
                'qte_demandee' => $ligne['qte_demandee'],
                'qte_expediee' => 0,
            ]);
        }
    }

    private function authorizeBon(Request $request, BonCommandeDepot $commande): void
    {
        $depotKey = UserAccess::depotKey($request->user());
        if ($depotKey && $commande->depot_demandeur !== $depotKey) {
            abort(403);
        }
    }

    /**
     * @return array<string, string>
     */
    public static function statutLabels(): array
    {
        return [
            'brouillon' => 'Brouillon',
            'envoye' => 'Envoyé',
            'converti' => 'Converti',
            'suspendu' => 'Suspendu',
            'expedie' => 'Expédié',
            'recu' => 'Reçu',
            'annule' => 'Annulé',
        ];
    }
}
