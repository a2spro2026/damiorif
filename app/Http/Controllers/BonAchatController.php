<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Models\Fournisseur;
use App\Support\Depots;
use App\Support\Echeances;
use App\Support\TypesReglement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BonAchatController extends Controller
{
    public function index(): View
    {
        return view('fournisseurs.bon-achat.index', [
            'bons' => BonAchat::query()->with(['lignes', 'fournisseur'])->orderByDesc('id')->get(),
            'fournisseurs' => Fournisseur::query()->orderBy('nom_fournisseur')->get(['id', 'ref_frns', 'nom_fournisseur', 'ville', 'type_reglement']),
            'typesReglement' => TypesReglement::options(),
            'depots' => Depots::options(),
            'echeances' => Echeances::options(),
            'nextNumero' => BonAchat::nextNumero(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $fournisseur = Fournisseur::query()->findOrFail($data['fournisseur_id']);

        DB::transaction(function () use ($data, $fournisseur) {
            $totaux = $this->computeTotaux($data['lignes']);

            $bon = BonAchat::create([
                'date_bon' => $data['date_bon'],
                'numero_bon' => BonAchat::nextNumero(),
                'fournisseur_id' => $fournisseur->id,
                'nom_fournisseur' => $fournisseur->nom_fournisseur,
                'ville' => $fournisseur->ville,
                'type_reglement' => $data['type_reglement'] ?? $fournisseur->type_reglement,
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

        return redirect()->route('fournisseurs.bon_achat');
    }

    public function update(Request $request, BonAchat $bonAchat): RedirectResponse
    {
        $data = $this->validated($request);
        $fournisseur = Fournisseur::query()->findOrFail($data['fournisseur_id']);

        DB::transaction(function () use ($data, $fournisseur, $bonAchat) {
            $totaux = $this->computeTotaux($data['lignes']);
            $dejaPaye = round(((float) $bonAchat->montant) - ((float) $bonAchat->solde), 2);
            $nouveauSolde = max(0, round($totaux['montant'] - $dejaPaye, 2));

            $bonAchat->update([
                'date_bon' => $data['date_bon'],
                'fournisseur_id' => $fournisseur->id,
                'nom_fournisseur' => $fournisseur->nom_fournisseur,
                'ville' => $fournisseur->ville,
                'type_reglement' => $data['type_reglement'] ?? $fournisseur->type_reglement,
                'echeance' => $data['echeance'] ?? null,
                'depot' => $data['depot'] ?? null,
                'qte_totale' => $totaux['qte'],
                'montant' => $totaux['montant'],
                'solde' => $nouveauSolde,
            ]);

            $bonAchat->lignes()->delete();

            foreach ($data['lignes'] as $ligne) {
                $bonAchat->lignes()->create([
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

        return redirect()->route('fournisseurs.bon_achat');
    }

    public function destroy(BonAchat $bonAchat): RedirectResponse
    {
        $bonAchat->delete();

        return redirect()->route('fournisseurs.bon_achat');
    }

    public function print(BonAchat $bonAchat): View
    {
        $bonAchat->load('lignes', 'fournisseur');

        return view('fournisseurs.bon-achat.print', [
            'bon' => $bonAchat,
            'typesReglement' => TypesReglement::options(),
            'depots' => Depots::options(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'date_bon' => ['required', 'date'],
            'fournisseur_id' => ['required', 'exists:fournisseurs,id'],
            'type_reglement' => ['nullable', 'string', Rule::in(array_keys(TypesReglement::options()))],
            'echeance' => ['nullable', 'string', Rule::in(array_keys(Echeances::options()))],
            'depot' => ['nullable', 'string', Rule::in(array_keys(Depots::options()))],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.ref' => ['nullable', 'string', 'max:100'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.famille' => ['nullable', 'string', 'max:100'],
            'lignes.*.categorie' => ['nullable', 'string', 'max:100'],
            'lignes.*.qte' => ['required', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire' => ['required', 'numeric', 'min:0'],
        ], [
            'fournisseur_id.required' => 'Veuillez sélectionner un fournisseur.',
            'lignes.required' => 'Ajoutez au moins un article.',
            'lignes.*.designation.required' => 'La désignation est obligatoire.',
        ]);
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
