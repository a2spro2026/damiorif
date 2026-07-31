<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\Ville;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VilleController extends Controller
{
    public function index(): View
    {
        $this->syncExisting();

        $villes = Ville::query()->orderBy('nom')->get()->map(function (Ville $ville) {
            $nom = $ville->nom;

            return [
                'id' => $ville->id,
                'nom' => $nom,
                'fournisseurs' => Fournisseur::query()
                    ->whereRaw('LOWER(ville) = ?', [mb_strtolower($nom)])
                    ->count(),
                'clients' => Client::query()
                    ->whereRaw('LOWER(ville) = ?', [mb_strtolower($nom)])
                    ->count(),
                'created_at' => $ville->created_at,
            ];
        });

        return view('configuration.parametres.ville', [
            'villes' => $villes,
        ]);
    }

    private function syncExisting(): void
    {
        $noms = DB::table('fournisseurs')
            ->whereNotNull('ville')
            ->where('ville', '!=', '')
            ->pluck('ville')
            ->merge(
                DB::table('clients')
                    ->whereNotNull('ville')
                    ->where('ville', '!=', '')
                    ->pluck('ville')
            )
            ->unique(fn ($v) => mb_strtolower(trim($v)));

        foreach ($noms as $nom) {
            Ville::syncFrom($nom);
        }
    }
}
