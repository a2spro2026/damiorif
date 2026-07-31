<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\UniteMesure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProduitController extends Controller
{
    public function index(): View
    {
        return view('stock.fiche-produit.index', [
            'produits' => Produit::query()->orderByDesc('id')->get(),
            'nextRef' => Produit::nextRef(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Produit::create([
            'date_fiche' => now()->toDateString(),
            'ref_produit' => Produit::nextRef(),
            'nom_produit' => $data['nom_produit'],
            'unite' => $data['unite'] ?? null,
        ]);

        UniteMesure::syncFrom($data['unite'] ?? null);

        return redirect()->route('stock.fiche_produit');
    }

    public function update(Request $request, Produit $produit): RedirectResponse
    {
        $data = $this->validated($request);

        $produit->update([
            'nom_produit' => $data['nom_produit'],
            'unite' => $data['unite'] ?? null,
        ]);

        UniteMesure::syncFrom($data['unite'] ?? null);

        return redirect()->route('stock.fiche_produit');
    }

    public function destroy(Produit $produit): RedirectResponse
    {
        $produit->delete();

        return redirect()->route('stock.fiche_produit');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nom_produit' => ['required', 'string', 'max:255'],
            'unite' => ['nullable', 'string', 'max:50'],
        ], [
            'nom_produit.required' => 'Le nom du produit est obligatoire.',
        ]);
    }
}
