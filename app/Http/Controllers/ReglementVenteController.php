<?php

namespace App\Http\Controllers;

use App\Models\BonVente;
use App\Models\Client;
use App\Models\ReglementVente;
use App\Support\StatutsReglement;
use App\Support\TypesReglement;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReglementVenteController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        $clients = Client::query()
            ->forUser($user)
            ->orderBy('nom_client')
            ->get(['id', 'ref_client', 'nom_client', 'type_reglement', 'banque']);

        $bonsQuery = BonVente::query()
            ->where(function ($q) {
                $q->where('solde', '>', 0)
                    ->orWhereIn('id', function ($sub) {
                        $sub->select('bon_vente_id')->from('reglement_vente_lignes');
                    });
            })
            ->orderBy('date_bon');

        if ($depotKey) {
            $bonsQuery->where('depot', $depotKey);
        }

        $bonsNonSoldes = $bonsQuery->get(['id', 'client_id', 'numero_bon', 'date_bon', 'montant', 'solde', 'depot']);

        $reglementsQuery = ReglementVente::query()->with('lignes')->orderByDesc('id');
        if ($depotKey) {
            $reglementsQuery->whereHas('lignes.bonVente', fn ($q) => $q->where('depot', $depotKey));
        }

        $banques = Client::query()
            ->whereNotNull('banque')
            ->where('banque', '!=', '')
            ->distinct()
            ->orderBy('banque')
            ->pluck('banque');

        return view('clients.reglement-vente.index', [
            'reglements' => $reglementsQuery->get(),
            'clients' => $clients,
            'bonsNonSoldes' => $bonsNonSoldes,
            'banques' => $banques,
            'typesReglement' => TypesReglement::options(),
            'statuts' => StatutsReglement::options(),
            'statutColors' => StatutsReglement::colors(),
            'nextNumero' => ReglementVente::nextNumero(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $client = Client::query()->findOrFail($data['client_id']);
        $this->assertClientAccess($client);

        DB::transaction(function () use ($data, $client) {
            $lignes = $this->normalizeLignes($data['lignes']);
            $montantLignes = round(array_sum(array_column($lignes, 'montant_applique')), 2);
            $montant = isset($data['montant']) && $data['montant'] !== null && $data['montant'] !== ''
                ? round((float) $data['montant'], 2)
                : $montantLignes;

            $reglement = ReglementVente::create([
                'date_reglement' => $data['date_reglement'],
                'numero' => ReglementVente::nextNumero(),
                'client_id' => $client->id,
                'nom_client' => $client->nom_client,
                'type_reglement' => $data['type_reglement'] ?? $client->type_reglement,
                'banque' => $data['banque'] ?? $client->banque,
                'nom_tire' => $data['nom_tire'] ?? null,
                'montant' => $montant,
                'date_encaissement' => $data['date_encaissement'] ?? null,
                'statut' => $data['statut'] ?? StatutsReglement::default(),
            ]);

            foreach ($lignes as $ligne) {
                $bon = BonVente::query()->lockForUpdate()->findOrFail($ligne['bon_vente_id']);
                $this->assertBonPayable($bon, $client->id, $ligne['montant_applique']);

                $reglement->lignes()->create([
                    'bon_vente_id' => $bon->id,
                    'numero_bon' => $bon->numero_bon,
                    'montant_applique' => $ligne['montant_applique'],
                ]);

                $this->recalculateSolde($bon);
            }
        });

        return redirect()->route('clients.reglement_vente');
    }

    public function update(Request $request, ReglementVente $reglementVente): RedirectResponse
    {
        $this->assertReglementAccess($reglementVente);
        $data = $this->validated($request);
        $client = Client::query()->findOrFail($data['client_id']);
        $this->assertClientAccess($client);

        DB::transaction(function () use ($data, $client, $reglementVente) {
            $anciensBonIds = $reglementVente->lignes()->pluck('bon_vente_id')->all();
            $reglementVente->lignes()->delete();

            $lignes = $this->normalizeLignes($data['lignes']);
            $montantLignes = round(array_sum(array_column($lignes, 'montant_applique')), 2);
            $montant = isset($data['montant']) && $data['montant'] !== null && $data['montant'] !== ''
                ? round((float) $data['montant'], 2)
                : $montantLignes;

            $reglementVente->update([
                'date_reglement' => $data['date_reglement'],
                'client_id' => $client->id,
                'nom_client' => $client->nom_client,
                'type_reglement' => $data['type_reglement'] ?? $client->type_reglement,
                'banque' => $data['banque'] ?? $client->banque,
                'nom_tire' => $data['nom_tire'] ?? null,
                'montant' => $montant,
                'date_encaissement' => $data['date_encaissement'] ?? null,
                'statut' => $data['statut'] ?? StatutsReglement::default(),
            ]);

            $bonsTouches = $anciensBonIds;

            foreach ($lignes as $ligne) {
                $bon = BonVente::query()->lockForUpdate()->findOrFail($ligne['bon_vente_id']);
                $this->assertBonPayable($bon, $client->id, $ligne['montant_applique']);

                $reglementVente->lignes()->create([
                    'bon_vente_id' => $bon->id,
                    'numero_bon' => $bon->numero_bon,
                    'montant_applique' => $ligne['montant_applique'],
                ]);

                $bonsTouches[] = $bon->id;
            }

            foreach (array_unique($bonsTouches) as $bonId) {
                $bon = BonVente::query()->lockForUpdate()->find($bonId);
                if ($bon) {
                    $this->recalculateSolde($bon);
                }
            }
        });

        return redirect()->route('clients.reglement_vente');
    }

    public function updateStatut(Request $request, ReglementVente $reglementVente): RedirectResponse
    {
        $this->assertReglementAccess($reglementVente);
        $data = $request->validate([
            'statut' => ['required', 'string', Rule::in(array_keys(StatutsReglement::options()))],
        ]);

        $reglementVente->update([
            'statut' => $data['statut'],
        ]);

        return redirect()->route('clients.reglement_vente');
    }

    public function destroy(ReglementVente $reglementVente): RedirectResponse
    {
        $this->assertReglementAccess($reglementVente);
        DB::transaction(function () use ($reglementVente) {
            $bonIds = $reglementVente->lignes()->pluck('bon_vente_id')->all();
            $reglementVente->delete();

            foreach (array_unique($bonIds) as $bonId) {
                $bon = BonVente::query()->lockForUpdate()->find($bonId);
                if ($bon) {
                    $this->recalculateSolde($bon);
                }
            }
        });

        return redirect()->route('clients.reglement_vente');
    }

    public function print(ReglementVente $reglementVente): View
    {
        $this->assertReglementAccess($reglementVente);
        $reglementVente->load('lignes', 'client');

        return view('clients.reglement-vente.print', [
            'reglement' => $reglementVente,
            'typesReglement' => TypesReglement::options(),
            'statuts' => StatutsReglement::options(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'date_reglement' => ['required', 'date'],
            'client_id' => ['required', 'exists:clients,id'],
            'type_reglement' => ['nullable', 'string', Rule::in(array_keys(TypesReglement::options()))],
            'banque' => ['nullable', 'string', 'max:150'],
            'nom_tire' => ['nullable', 'string', 'max:255'],
            'montant' => ['nullable', 'numeric', 'min:0'],
            'date_encaissement' => ['nullable', 'date'],
            'statut' => ['required', 'string', Rule::in(array_keys(StatutsReglement::options()))],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.bon_vente_id' => ['required', 'exists:bons_vente,id'],
            'lignes.*.montant_applique' => ['required', 'numeric', 'min:0.01'],
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'lignes.required' => 'Ajoutez au moins un bon non soldé.',
            'lignes.min' => 'Ajoutez au moins un bon non soldé.',
        ]);
    }

    /**
     * @param  array<int, array{bon_vente_id: mixed, montant_applique: mixed}>  $lignes
     * @return array<int, array{bon_vente_id: int, montant_applique: float}>
     */
    private function normalizeLignes(array $lignes): array
    {
        $normalized = [];

        foreach ($lignes as $ligne) {
            $bonId = (int) $ligne['bon_vente_id'];
            $montant = round((float) $ligne['montant_applique'], 2);

            if ($montant <= 0) {
                continue;
            }

            if (isset($normalized[$bonId])) {
                $normalized[$bonId]['montant_applique'] = round($normalized[$bonId]['montant_applique'] + $montant, 2);
            } else {
                $normalized[$bonId] = [
                    'bon_vente_id' => $bonId,
                    'montant_applique' => $montant,
                ];
            }
        }

        if ($normalized === []) {
            throw ValidationException::withMessages([
                'lignes' => 'Ajoutez au moins un bon non soldé.',
            ]);
        }

        return array_values($normalized);
    }

    private function assertBonPayable(BonVente $bon, int $clientId, float $montant): void
    {
        if ((int) $bon->client_id !== $clientId) {
            throw ValidationException::withMessages([
                'lignes' => "Le bon {$bon->numero_bon} n'appartient pas à ce client.",
            ]);
        }

        $depotKey = UserAccess::depotKey(auth()->user());
        if ($depotKey && $bon->depot !== $depotKey) {
            throw ValidationException::withMessages([
                'lignes' => "Le bon {$bon->numero_bon} n'appartient pas à votre dépôt.",
            ]);
        }

        $dejaRegle = (float) DB::table('reglement_vente_lignes')
            ->where('bon_vente_id', $bon->id)
            ->sum('montant_applique');

        $soldeDispo = round(((float) $bon->montant) - $dejaRegle, 2);

        if ($montant - $soldeDispo > 0.009) {
            throw ValidationException::withMessages([
                'lignes' => "Le montant dépasse le solde du bon {$bon->numero_bon} (solde : {$soldeDispo}).",
            ]);
        }
    }

    private function assertReglementAccess(ReglementVente $reglement): void
    {
        $depotKey = UserAccess::depotKey(auth()->user());
        if (! $depotKey) {
            return;
        }

        $total = $reglement->lignes()->count();
        $allowed = $reglement->lignes()
            ->whereHas('bonVente', fn ($q) => $q->where('depot', $depotKey))
            ->count();

        if ($total === 0 || $allowed !== $total) {
            abort(403, 'Ce réglement n\'appartient pas à votre dépôt.');
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

    private function recalculateSolde(BonVente $bon): void
    {
        $totalRegle = (float) DB::table('reglement_vente_lignes')
            ->where('bon_vente_id', $bon->id)
            ->sum('montant_applique');

        $bon->update([
            'solde' => max(0, round(((float) $bon->montant) - $totalRegle, 2)),
        ]);
    }
}
