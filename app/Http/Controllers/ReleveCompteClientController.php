<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Support\ReleveCompteService;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReleveCompteClientController extends Controller
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
        $user = $request->user();
        $mois = $request->query('mois');
        $clientId = $request->query('client_id') ? (int) $request->query('client_id') : null;
        $depotKey = UserAccess::depotKey($user);

        $data = ReleveCompteService::clients($mois, $clientId, $depotKey);

        $tiersList = Client::query()->forUser($user)->orderBy('nom_client')->get(['id', 'nom_client']);
        $selectedTiers = $clientId ? $tiersList->firstWhere('id', $clientId) : null;

        $payload = [
            'title' => 'Relevé Compte',
            'tiersLabel' => 'Client',
            'tiersField' => 'client_id',
            'tiersList' => $tiersList,
            'selectedTiersId' => $clientId,
            'selectedTiersName' => $selectedTiers?->nom_client,
            'selectedMois' => $mois,
            'monthOptions' => ReleveCompteService::monthOptions(),
            'rows' => $data['rows'],
            'totalDebitLabel' => 'Total Vente',
            'totalAchats' => $data['totalAchats'],
            'totalPaye' => $data['totalPaye'],
            'totalSolde' => $data['totalSolde'],
            'indexRoute' => 'clients.releve_compte',
            'printRoute' => 'clients.releve_compte.print',
            'closeRoute' => 'dashboard',
            'depotLabel' => UserAccess::depotLabel($user),
        ];

        return view($print ? 'clients.releve-compte.print' : 'clients.releve-compte.index', $payload);
    }
}
