<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Support\Depots;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepotStockController extends Controller
{
    public function show(string $depot): View
    {
        $options = Depots::options();
        abort_unless(array_key_exists($depot, $options), 404);

        $user = auth()->user();
        $userDepot = UserAccess::depotKey($user);
        if ($userDepot && $userDepot !== $depot) {
            abort(403);
        }

        $achats = BonAchat::query()
            ->with('lignes')
            ->where('depot', $depot)
            ->orderByDesc('date_bon')
            ->orderByDesc('id')
            ->get();

        $achatLignes = collect();
        foreach ($achats as $bon) {
            foreach ($bon->lignes as $ligne) {
                $achatLignes->push([
                    'date' => $bon->date_bon,
                    'numero_bon' => $bon->numero_bon,
                    'fournisseur' => $bon->nom_fournisseur,
                    'ref' => $ligne->ref ?: '—',
                    'designation' => $ligne->designation,
                    'qte' => (float) $ligne->qte,
                    'prix_unitaire' => (float) $ligne->prix_unitaire,
                    'montant' => (float) $ligne->sous_total,
                ]);
            }
        }

        return view('stock.depot.index', [
            'depot' => $depot,
            'depotLabel' => $options[$depot],
            'achatLignes' => $achatLignes,
            'totalAchatsMontant' => round((float) $achatLignes->sum('montant'), 2),
            'totalAchatsQte' => round((float) $achatLignes->sum('qte'), 3),
        ]);
    }

    public function showByRequest(Request $request): View
    {
        $depot = (string) $request->route('depot', '');

        return $this->show($depot);
    }
}
