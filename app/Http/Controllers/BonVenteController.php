<?php

namespace App\Http\Controllers;

use App\Models\BonVente;
use App\Models\Client;
use App\Support\Echeances;
use App\Support\StockDepotService;
use App\Support\TypesReglement;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BonVenteController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        $query = BonVente::query()->with(['lignes', 'client'])->orderByDesc('id');
        if ($depotKey) {
            $query->where('depot', $depotKey);
        }

        $bons = $query->get();
        $totalVentes = round((float) $bons->sum('montant'), 2);
        $totalSolde = round((float) $bons->sum('solde'), 2);

        $depotOptions = UserAccess::depotOptionsFor($user);
        $stockByDepot = [];
        foreach (array_keys($depotOptions) as $depot) {
            $stockByDepot[$depot] = StockDepotService::stockMapForDepot($depot);
        }

        return view('clients.bon-vente.index', [
            'bons' => $bons,
            'clients' => Client::query()->forUser($user)->orderBy('nom_client')->get(['id', 'ref_client', 'nom_client', 'ville', 'type_reglement']),
            'typesReglement' => TypesReglement::options(),
            'depots' => $depotOptions,
            'stockByDepot' => $stockByDepot,
            'lockedDepot' => $depotKey,
            'echeances' => Echeances::options(),
            'nextNumero' => BonVente::nextNumero(),
            'totalAchats' => $totalVentes,
            'totalPaiement' => round($totalVentes - $totalSolde, 2),
            'totalSolde' => $totalSolde,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $client = Client::query()->findOrFail($data['client_id']);
        $this->assertClientAccess($client);
        $this->assertStockAvailable($data['depot'] ?? '', $data['lignes']);

        DB::transaction(function () use ($data, $client) {
            $totaux = $this->computeTotaux($data['lignes']);

            $bon = BonVente::create([
                'date_bon' => $data['date_bon'],
                'numero_bon' => BonVente::nextNumero(),
                'client_id' => $client->id,
                'nom_client' => $client->nom_client,
                'ville' => $client->ville,
                'type_reglement' => $data['type_reglement'] ?? $client->type_reglement,
                'echeance' => $data['echeance'] ?? null,
                'depot' => $data['depot'] ?? null,
                'qte_totale' => $totaux['qte'],
                'montant' => $totaux['montant'],
                'solde' => $totaux['montant'],
            ]);

            foreach ($data['lignes'] as $ligne) {
                $bon->lignes()->create([
                    'ref' => $ligne['ref'] ?? null,
                    'designation' => $ligne['designation'],
                    'famille' => $ligne['famille'] ?? null,
                    'categorie' => $ligne['categorie'] ?? null,
                    'qte' => $ligne['qte'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'sous_total' => round(((float) $ligne['qte']) * ((float) $ligne['prix_unitaire']), 2),
                ]);
            }
        });

        return redirect()->route('clients.bon_vente');
    }

    public function update(Request $request, BonVente $bonVente): RedirectResponse
    {
        $this->assertDepotAccess($bonVente);
        $data = $this->validated($request);
        $client = Client::query()->findOrFail($data['client_id']);
        $this->assertClientAccess($client);
        $this->assertStockAvailable($data['depot'] ?? '', $data['lignes'], $bonVente->id);

        DB::transaction(function () use ($data, $client, $bonVente) {
            $totaux = $this->computeTotaux($data['lignes']);
            $dejaPaye = round(((float) $bonVente->montant) - ((float) $bonVente->solde), 2);
            $nouveauSolde = max(0, round($totaux['montant'] - $dejaPaye, 2));

            $bonVente->update([
                'date_bon' => $data['date_bon'],
                'client_id' => $client->id,
                'nom_client' => $client->nom_client,
                'ville' => $client->ville,
                'type_reglement' => $data['type_reglement'] ?? $client->type_reglement,
                'echeance' => $data['echeance'] ?? null,
                'depot' => $data['depot'] ?? null,
                'qte_totale' => $totaux['qte'],
                'montant' => $totaux['montant'],
                'solde' => $nouveauSolde,
            ]);

            $bonVente->lignes()->delete();

            foreach ($data['lignes'] as $ligne) {
                $bonVente->lignes()->create([
                    'ref' => $ligne['ref'] ?? null,
                    'designation' => $ligne['designation'],
                    'famille' => $ligne['famille'] ?? null,
                    'categorie' => $ligne['categorie'] ?? null,
                    'qte' => $ligne['qte'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'sous_total' => round(((float) $ligne['qte']) * ((float) $ligne['prix_unitaire']), 2),
                ]);
            }
        });

        return redirect()->route('clients.bon_vente');
    }

    public function destroy(BonVente $bonVente): RedirectResponse
    {
        $this->assertDepotAccess($bonVente);
        $bonVente->delete();

        return redirect()->route('clients.bon_vente');
    }

    public function print(BonVente $bonVente): View
    {
        $this->assertDepotAccess($bonVente);
        $bonVente->load('lignes', 'client');

        return view('clients.bon-vente.print', [
            'bon' => $bonVente,
            'typesReglement' => TypesReglement::options(),
            'depots' => UserAccess::depotOptionsFor(auth()->user()),
        ]);
    }

    private function validated(Request $request): array
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);
        $allowedDepots = array_keys(UserAccess::depotOptionsFor($user));

        $data = $request->validate([
            'date_bon' => ['required', 'date'],
            'client_id' => ['required', 'exists:clients,id'],
            'type_reglement' => ['nullable', 'string', Rule::in(array_keys(TypesReglement::options()))],
            'echeance' => ['nullable', 'string', Rule::in(array_keys(Echeances::options()))],
            'depot' => ['nullable', 'string', Rule::in($allowedDepots)],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.ref' => ['nullable', 'string', 'max:100'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.famille' => ['nullable', 'string', 'max:100'],
            'lignes.*.categorie' => ['nullable', 'string', 'max:100'],
            'lignes.*.qte' => ['required', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire' => ['required', 'numeric', 'min:0'],
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'lignes.required' => 'Ajoutez au moins un article.',
            'lignes.*.designation.required' => 'La désignation est obligatoire.',
        ]);

        if ($depotKey) {
            $data['depot'] = $depotKey;
        }

        return $data;
    }

    private function assertDepotAccess(BonVente $bon): void
    {
        $depotKey = UserAccess::depotKey(auth()->user());
        if ($depotKey && $bon->depot !== $depotKey) {
            abort(403, 'Ce bon n\'appartient pas à votre dépôt.');
        }
    }

    private function assertClientAccess(Client $client): void
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);
        if ($depotKey && ($client->depot !== $depotKey || (int) $client->user_id !== (int) $user->id)) {
            abort(403, 'Ce client n\'appartient pas à votre dépôt.');
        }
    }

    private function assertStockAvailable(string $depotKey, array $lignes, ?int $excludeBonVenteId = null): void
    {
        $errors = StockDepotService::saleStockErrors($depotKey, $lignes, $excludeBonVenteId);
        if ($errors !== []) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }

    private function computeTotaux(array $lignes): array
    {
        $qte = 0;
        $montant = 0;

        foreach ($lignes as $ligne) {
            $ligneQte = (float) $ligne['qte'];
            $lignePu = (float) $ligne['prix_unitaire'];
            $qte += $ligneQte;
            $montant += $ligneQte * $lignePu;
        }

        return [
            'qte' => round($qte, 2),
            'montant' => round($montant, 2),
        ];
    }
}
