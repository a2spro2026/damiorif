<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\Reglement;
use App\Models\Ville;
use App\Support\TypesReglement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FournisseurController extends Controller
{
    public function index(): View
    {
        return view('fournisseurs.fiche.index', [
            'fournisseurs' => Fournisseur::query()->orderByDesc('id')->get(),
            'typesReglement' => TypesReglement::options(),
            'nextRef' => Fournisseur::nextRef(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Fournisseur::create([
            'date_fiche' => now()->toDateString(),
            'ref_frns' => Fournisseur::nextRef(),
            'nom_fournisseur' => $data['nom_fournisseur'],
            'nom_gerant' => $data['nom_gerant'] ?? null,
            'contact' => $data['contact'] ?? null,
            'ville' => $data['ville'] ?? null,
            'type_reglement' => $data['type_reglement'] ?? null,
            'banque' => $data['banque'] ?? null,
            'rib' => $data['rib'],
        ]);

        Ville::syncFrom($data['ville'] ?? null);
        Reglement::syncFrom(TypesReglement::label($data['type_reglement'] ?? null));

        return redirect()->route('fournisseurs.fiche');
    }

    public function update(Request $request, Fournisseur $fournisseur): RedirectResponse
    {
        $data = $this->validated($request, $fournisseur);

        $fournisseur->update([
            'nom_fournisseur' => $data['nom_fournisseur'],
            'nom_gerant' => $data['nom_gerant'] ?? null,
            'contact' => $data['contact'] ?? null,
            'ville' => $data['ville'] ?? null,
            'type_reglement' => $data['type_reglement'] ?? null,
            'banque' => $data['banque'] ?? null,
            'rib' => $data['rib'],
        ]);

        Ville::syncFrom($data['ville'] ?? null);
        Reglement::syncFrom(TypesReglement::label($data['type_reglement'] ?? null));

        return redirect()->route('fournisseurs.fiche');
    }

    public function destroy(Fournisseur $fournisseur): RedirectResponse
    {
        $fournisseur->delete();

        return redirect()
            ->route('fournisseurs.fiche')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }

    private function validated(Request $request, ?Fournisseur $fournisseur = null): array
    {
        return $request->validate([
            'nom_fournisseur' => ['required', 'string', 'max:255'],
            'nom_gerant' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:50'],
            'ville' => ['nullable', 'string', 'max:100'],
            'type_reglement' => ['nullable', 'string', Rule::in(array_keys(TypesReglement::options()))],
            'banque' => ['nullable', 'string', 'max:100'],
            'rib' => ['required', 'digits:24'],
        ], [
            'nom_fournisseur.required' => 'Le nom du fournisseur est obligatoire.',
            'rib.required' => 'Le RIB est obligatoire.',
            'rib.digits' => 'Le RIB doit contenir exactement 24 chiffres.',
        ]);
    }
}
