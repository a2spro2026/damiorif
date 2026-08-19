<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\Reglement;
use App\Support\TypesReglement;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReglementController extends Controller
{
    public function index(): View
    {
        $this->syncExisting();

        $reglements = Reglement::query()->orderBy('nom')->get()->map(function (Reglement $reglement) {
            $nom = $reglement->nom;
            $key = TypesReglement::keyFromLabel($nom);

            $fournisseurs = Fournisseur::query()
                ->where(function ($q) use ($nom, $key) {
                    $q->whereRaw('LOWER(type_reglement) = ?', [mb_strtolower($nom)]);
                    if ($key) {
                        $q->orWhere('type_reglement', $key);
                    }
                })
                ->count();

            $clients = Client::query()
                ->where(function ($q) use ($nom, $key) {
                    $q->whereRaw('LOWER(type_reglement) = ?', [mb_strtolower($nom)]);
                    if ($key) {
                        $q->orWhere('type_reglement', $key);
                    }
                })
                ->count();

            return [
                'id' => $reglement->id,
                'nom' => $nom,
                'fournisseurs' => $fournisseurs,
                'clients' => $clients,
                'created_at' => $reglement->created_at,
            ];
        });

        return view('configuration.parametres.reglement', [
            'reglements' => $reglements,
        ]);
    }

    private function syncExisting(): void
    {
        $keys = DB::table('fournisseurs')
            ->whereNotNull('type_reglement')
            ->where('type_reglement', '!=', '')
            ->pluck('type_reglement')
            ->merge(
                DB::table('clients')
                    ->whereNotNull('type_reglement')
                    ->where('type_reglement', '!=', '')
                    ->pluck('type_reglement')
            )
            ->unique();

        foreach ($keys as $key) {
            Reglement::syncFrom(TypesReglement::label($key));
        }
    }
}
