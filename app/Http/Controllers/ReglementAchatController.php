<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Models\Fournisseur;
use App\Models\ReglementAchat;
use App\Support\StatutsReglement;
use App\Support\TypesReglement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReglementAchatController extends Controller
{
    public function index(): View
    {
        $fournisseurs = Fournisseur::query()
            ->orderBy('nom_fournisseur')
            ->get(['id', 'ref_frns', 'nom_fournisseur', 'type_reglement', 'banque']);

        $bonsNonSoldes = BonAchat::query()
            ->where(function ($q) {
                $q->where('solde', '>', 0)
                    ->orWhereIn('id', function ($sub) {
                        $sub->select('bon_achat_id')->from('reglement_achat_lignes');
                    });
            })
            ->orderBy('date_bon')
            ->get(['id', 'fournisseur_id', 'numero_bon', 'date_bon', 'montant', 'solde']);

        $banques = Fournisseur::query()
            ->whereNotNull('banque')
            ->where('banque', '!=', '')
            ->distinct()
            ->orderBy('banque')
            ->pluck('banque');

        $reglements = ReglementAchat::query()->with('lignes')->orderByDesc('id')->get();

        return view('fournisseurs.reglement-achat.index', [
            'reglements' => $reglements,
            'fournisseurs' => $fournisseurs,
            'bonsNonSoldes' => $bonsNonSoldes,
            'banques' => $banques,
            'typesReglement' => TypesReglement::options(),
            'statuts' => StatutsReglement::options(),
            'statutColors' => StatutsReglement::colors(),
            'nextNumero' => ReglementAchat::nextNumero(),
            'totauxType' => TypesReglement::sumByFamily($reglements),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $fournisseur = Fournisseur::query()->findOrFail($data['fournisseur_id']);

        DB::transaction(function () use ($data, $fournisseur) {
            $lignes = $this->normalizeLignes($data['lignes']);
            $montantLignes = round(array_sum(array_column($lignes, 'montant_applique')), 2);
            $montant = isset($data['montant']) && $data['montant'] !== null && $data['montant'] !== ''
                ? round((float) $data['montant'], 2)
                : $montantLignes;

            $reglement = ReglementAchat::create([
                'date_reglement' => $data['date_reglement'],
                'numero' => ReglementAchat::nextNumero(),
                'fournisseur_id' => $fournisseur->id,
                'nom_fournisseur' => $fournisseur->nom_fournisseur,
                'type_reglement' => $data['type_reglement'] ?? $fournisseur->type_reglement,
                'banque' => $data['banque'] ?? $fournisseur->banque,
                'nom_tire' => $data['nom_tire'] ?? null,
                'montant' => $montant,
                'date_decaissement' => $data['date_decaissement'] ?? null,
                'statut' => $data['statut'] ?? StatutsReglement::default(),
            ]);

            foreach ($lignes as $ligne) {
                $bon = BonAchat::query()->lockForUpdate()->findOrFail($ligne['bon_achat_id']);
                $this->assertBonPayable($bon, $fournisseur->id, $ligne['montant_applique']);

                $reglement->lignes()->create([
                    'bon_achat_id' => $bon->id,
                    'numero_bon' => $bon->numero_bon,
                    'montant_applique' => $ligne['montant_applique'],
                ]);

                $this->recalculateSolde($bon);
            }
        });

        return redirect()->route('fournisseurs.reglement_achat');
    }

    public function update(Request $request, ReglementAchat $reglementAchat): RedirectResponse
    {
        $data = $this->validated($request);
        $fournisseur = Fournisseur::query()->findOrFail($data['fournisseur_id']);

        DB::transaction(function () use ($data, $fournisseur, $reglementAchat) {
            $anciensBonIds = $reglementAchat->lignes()->pluck('bon_achat_id')->all();
            $reglementAchat->lignes()->delete();

            $lignes = $this->normalizeLignes($data['lignes']);
            $montantLignes = round(array_sum(array_column($lignes, 'montant_applique')), 2);
            $montant = isset($data['montant']) && $data['montant'] !== null && $data['montant'] !== ''
                ? round((float) $data['montant'], 2)
                : $montantLignes;

            $reglementAchat->update([
                'date_reglement' => $data['date_reglement'],
                'fournisseur_id' => $fournisseur->id,
                'nom_fournisseur' => $fournisseur->nom_fournisseur,
                'type_reglement' => $data['type_reglement'] ?? $fournisseur->type_reglement,
                'banque' => $data['banque'] ?? $fournisseur->banque,
                'nom_tire' => $data['nom_tire'] ?? null,
                'montant' => $montant,
                'date_decaissement' => $data['date_decaissement'] ?? null,
                'statut' => $data['statut'] ?? StatutsReglement::default(),
            ]);

            $bonsTouches = $anciensBonIds;

            foreach ($lignes as $ligne) {
                $bon = BonAchat::query()->lockForUpdate()->findOrFail($ligne['bon_achat_id']);
                $this->assertBonPayable($bon, $fournisseur->id, $ligne['montant_applique']);

                $reglementAchat->lignes()->create([
                    'bon_achat_id' => $bon->id,
                    'numero_bon' => $bon->numero_bon,
                    'montant_applique' => $ligne['montant_applique'],
                ]);

                $bonsTouches[] = $bon->id;
            }

            foreach (array_unique($bonsTouches) as $bonId) {
                $bon = BonAchat::query()->lockForUpdate()->find($bonId);
                if ($bon) {
                    $this->recalculateSolde($bon);
                }
            }
        });

        return redirect()->route('fournisseurs.reglement_achat');
    }

    public function updateStatut(Request $request, ReglementAchat $reglementAchat): RedirectResponse
    {
        $data = $request->validate([
            'statut' => ['required', 'string', Rule::in(array_keys(StatutsReglement::options()))],
        ]);

        $reglementAchat->update([
            'statut' => $data['statut'],
        ]);

        return redirect()->route('fournisseurs.reglement_achat');
    }

    public function destroy(ReglementAchat $reglementAchat): RedirectResponse
    {
        DB::transaction(function () use ($reglementAchat) {
            $bonIds = $reglementAchat->lignes()->pluck('bon_achat_id')->all();
            $reglementAchat->delete();

            foreach (array_unique($bonIds) as $bonId) {
                $bon = BonAchat::query()->lockForUpdate()->find($bonId);
                if ($bon) {
                    $this->recalculateSolde($bon);
                }
            }
        });

        return redirect()->route('fournisseurs.reglement_achat');
    }

    public function print(ReglementAchat $reglementAchat): View
    {
        $reglementAchat->load('lignes', 'fournisseur');

        return view('fournisseurs.reglement-achat.print', [
            'reglement' => $reglementAchat,
            'typesReglement' => TypesReglement::options(),
            'statuts' => StatutsReglement::options(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'date_reglement' => ['required', 'date'],
            'fournisseur_id' => ['required', 'exists:fournisseurs,id'],
            'type_reglement' => ['nullable', 'string', Rule::in(array_keys(TypesReglement::options()))],
            'banque' => ['nullable', 'string', 'max:150'],
            'nom_tire' => ['nullable', 'string', 'max:255'],
            'montant' => ['nullable', 'numeric', 'min:0'],
            'date_decaissement' => ['nullable', 'date'],
            'statut' => ['required', 'string', Rule::in(array_keys(StatutsReglement::options()))],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.bon_achat_id' => ['required', 'exists:bons_achat,id'],
            'lignes.*.montant_applique' => ['required', 'numeric', 'min:0.01'],
        ], [
            'fournisseur_id.required' => 'Veuillez sélectionner un fournisseur.',
            'lignes.required' => 'Ajoutez au moins un bon non soldé.',
            'lignes.min' => 'Ajoutez au moins un bon non soldé.',
        ]);
    }

    /**
     * @param  array<int, array{bon_achat_id: mixed, montant_applique: mixed}>  $lignes
     * @return array<int, array{bon_achat_id: int, montant_applique: float}>
     */
    private function normalizeLignes(array $lignes): array
    {
        $normalized = [];

        foreach ($lignes as $ligne) {
            $bonId = (int) $ligne['bon_achat_id'];
            $montant = round((float) $ligne['montant_applique'], 2);

            if ($montant <= 0) {
                continue;
            }

            if (isset($normalized[$bonId])) {
                $normalized[$bonId]['montant_applique'] = round($normalized[$bonId]['montant_applique'] + $montant, 2);
            } else {
                $normalized[$bonId] = [
                    'bon_achat_id' => $bonId,
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

    private function assertBonPayable(BonAchat $bon, int $fournisseurId, float $montant): void
    {
        if ((int) $bon->fournisseur_id !== $fournisseurId) {
            throw ValidationException::withMessages([
                'lignes' => "Le bon {$bon->numero_bon} n'appartient pas à ce fournisseur.",
            ]);
        }

        $dejaRegle = (float) DB::table('reglement_achat_lignes')
            ->where('bon_achat_id', $bon->id)
            ->sum('montant_applique');

        $soldeDispo = round(((float) $bon->montant) - $dejaRegle, 2);

        if ($montant - $soldeDispo > 0.009) {
            throw ValidationException::withMessages([
                'lignes' => "Le montant dépasse le solde du bon {$bon->numero_bon} (solde : {$soldeDispo}).",
            ]);
        }
    }

    private function recalculateSolde(BonAchat $bon): void
    {
        $totalRegle = (float) DB::table('reglement_achat_lignes')
            ->where('bon_achat_id', $bon->id)
            ->sum('montant_applique');

        $bon->update([
            'solde' => max(0, round(((float) $bon->montant) - $totalRegle, 2)),
        ]);
    }
}
