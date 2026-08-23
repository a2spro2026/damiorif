<?php

namespace App\Http\Controllers;

use App\Support\Depots;
use App\Support\ManquesDepotService;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockManquesController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $depotKey = UserAccess::depotKey($user);
        $selectedDepot = $depotKey ?: $request->query('depot');
        $selectedMois = $request->query('mois');

        if ($selectedDepot && ! Depots::isRegional($selectedDepot)) {
            $selectedDepot = null;
        }

        $rows = ManquesDepotService::regionalManques($selectedDepot, $selectedMois);

        $depotOptions = $depotKey
            ? array_intersect_key(Depots::options(), array_flip([$depotKey]))
            : array_intersect_key(Depots::options(), array_flip(Depots::regionalKeys()));

        return view('stock.manques.index', [
            'rows' => $rows,
            'depotOptions' => $depotOptions,
            'selectedDepot' => $selectedDepot,
            'selectedMois' => $selectedMois,
            'monthOptions' => ManquesDepotService::monthOptions(),
            'totalManque' => round((float) $rows->sum('manque'), 3),
            'totalLignes' => $rows->count(),
            'isCentralView' => $depotKey === null,
        ]);
    }
}
