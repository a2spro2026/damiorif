<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Support\ReleveCompteService;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReleveCompteFournisseurController extends Controller
{
    public function index(Request $request): View
    {
        return $this->render($request, false);
    }

    public function print(Request $request): View
    {
        return $this->render($request, true);
    }

    private function render(Request $request, bool $print): View
    {
        $mois = $request->query('mois');
        $fournisseurId = $request->query('fournisseur_id') ? (int) $request->query('fournisseur_id') : null;

        $data = ReleveCompteService::fournisseurs($mois, $fournisseurId);

        $tiersList = Fournisseur::query()->orderBy('nom_fournisseur')->get(['id', 'nom_fournisseur']);
        $selectedTiers = $fournisseurId ? $tiersList->firstWhere('id', $fournisseurId) : null;

        $payload = [
            'title' => 'Relevé Compte',
            'tiersLabel' => 'Fournisseur',
            'tiersField' => 'fournisseur_id',
            'tiersList' => $tiersList,
            'selectedTiersId' => $fournisseurId,
            'selectedTiersName' => $selectedTiers?->nom_fournisseur,
            'selectedMois' => $mois,
            'monthOptions' => ReleveCompteService::monthOptions(),
            'rows' => $data['rows'],
            'totalAchats' => $data['totalAchats'],
            'totalPaye' => $data['totalPaye'],
            'totalSolde' => $data['totalSolde'],
            'indexRoute' => 'fournisseurs.releve_compte',
            'printRoute' => 'fournisseurs.releve_compte.print',
            'closeRoute' => 'dashboard',
            'depotLabel' => UserAccess::depotLabel($request->user()),
        ];

        return view($print ? 'fournisseurs.releve-compte.print' : 'fournisseurs.releve-compte.index', $payload);
    }
}
