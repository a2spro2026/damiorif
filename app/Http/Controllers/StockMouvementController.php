<?php

namespace App\Http\Controllers;

use App\Support\Depots;
use App\Support\StockMouvementReleveService;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMouvementController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);
        $depotOptions = UserAccess::depotOptionsFor($user);

        $selectedDepot = $depotKey ?? (string) $request->query('depot', Depots::centralKey());
        if (! array_key_exists($selectedDepot, $depotOptions)) {
            $selectedDepot = array_key_first($depotOptions) ?: Depots::centralKey();
        }

        $selectedYear = (int) $request->query('annee', (int) now()->format('Y'));
        if ($selectedYear < 2000 || $selectedYear > 2100) {
            $selectedYear = (int) now()->format('Y');
        }

        $releveRows = StockMouvementReleveService::ventesMensuellesForDepot($selectedDepot, $selectedYear);

        $yearOptions = [];
        $currentYear = (int) now()->format('Y');
        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
            $yearOptions[] = $y;
        }

        return view('stock.mouvement.index', [
            'releveRows' => $releveRows,
            'depotOptions' => $depotOptions,
            'selectedDepot' => $selectedDepot,
            'selectedYear' => $selectedYear,
            'yearOptions' => $yearOptions,
            'monthLabels' => StockMouvementReleveService::monthShortLabels(),
            'depotLabel' => $depotOptions[$selectedDepot] ?? $selectedDepot,
            'lockedDepot' => $depotKey,
        ]);
    }
}
