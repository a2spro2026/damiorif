<?php

namespace App\Http\Controllers;

use App\Support\Depots;
use App\Support\StockDepotService;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepotStockController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $depotKey = UserAccess::depotKey($user);
        $depotOptions = UserAccess::depotOptionsFor($user);

        $depot = $depotKey ?? (string) $request->query('depot', Depots::centralKey());
        if (! array_key_exists($depot, $depotOptions)) {
            $depot = array_key_first($depotOptions) ?: Depots::centralKey();
        }

        return $this->show($depot, $depotOptions);
    }

    public function show(string $depot, ?array $depotOptions = null): View
    {
        $options = Depots::options();
        abort_unless(array_key_exists($depot, $options), 404);

        $user = auth()->user();
        $userDepot = UserAccess::depotKey($user);
        if ($userDepot && $userDepot !== $depot) {
            abort(403);
        }

        $depotOptions ??= UserAccess::depotOptionsFor($user);
        $stockRows = StockDepotService::detailForDepot($depot);

        return view('stock.depot.index', [
            'depot' => $depot,
            'depotLabel' => $options[$depot],
            'depotOptions' => $depotOptions,
            'stockRows' => $stockRows,
            'lockedDepot' => $userDepot,
        ]);
    }
}
