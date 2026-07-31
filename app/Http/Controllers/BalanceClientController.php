<?php

namespace App\Http\Controllers;

use App\Models\BonVente;
use App\Support\UserAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BalanceClientController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        $query = BonVente::query()
            ->select(
                'client_id',
                'nom_client',
                DB::raw('COUNT(*) as nb_bons'),
                DB::raw('COALESCE(SUM(montant), 0) as total_montant'),
                DB::raw('COALESCE(SUM(solde), 0) as total_solde')
            )
            ->groupBy('client_id', 'nom_client')
            ->orderBy('nom_client');

        if ($depotKey) {
            $query->where('depot', $depotKey);
        }

        $rows = $query->get()->map(function ($row) {
            $montant = round((float) $row->total_montant, 2);
            $solde = round((float) $row->total_solde, 2);

            return [
                'client_id' => $row->client_id,
                'nom_client' => $row->nom_client,
                'nb_bons' => (int) $row->nb_bons,
                'montant' => $montant,
                'regle' => round($montant - $solde, 2),
                'solde' => $solde,
            ];
        });

        return view('clients.balance.index', [
            'rows' => $rows,
            'depotLabel' => UserAccess::depotLabel($user),
            'totalMontant' => round($rows->sum('montant'), 2),
            'totalRegle' => round($rows->sum('regle'), 2),
            'totalSolde' => round($rows->sum('solde'), 2),
        ]);
    }
}
