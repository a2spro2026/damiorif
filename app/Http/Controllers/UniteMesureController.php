<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\UniteMesure;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UniteMesureController extends Controller
{
    public function index(): View
    {
        $this->syncExisting();

        $unites = UniteMesure::query()->orderBy('nom')->get()->map(function (UniteMesure $unite) {
            $nom = $unite->nom;

            return [
                'id' => $unite->id,
                'nom' => $nom,
                'produits' => Produit::query()
                    ->whereRaw('LOWER(unite) = ?', [mb_strtolower($nom)])
                    ->count(),
                'created_at' => $unite->created_at,
            ];
        });

        return view('configuration.parametres.unite', [
            'unites' => $unites,
        ]);
    }

    private function syncExisting(): void
    {
        $noms = DB::table('produits')
            ->whereNotNull('unite')
            ->where('unite', '!=', '')
            ->pluck('unite')
            ->unique(fn ($v) => mb_strtolower(trim($v)));

        foreach ($noms as $nom) {
            UniteMesure::syncFrom($nom);
        }
    }
}
