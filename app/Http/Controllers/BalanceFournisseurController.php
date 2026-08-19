<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Support\UserAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BalanceFournisseurController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        $query = BonAchat::query()
            ->select(
                'fournisseur_id',
                'nom_fournisseur',
                DB::raw('COUNT(*) as nb_bons'),
                DB::raw('COALESCE(SUM(montant), 0) as total_montant'),
                DB::raw('COALESCE(SUM(solde), 0) as total_solde')
            )
            ->groupBy('fournisseur_id', 'nom_fournisseur')
            ->orderBy('nom_fournisseur');

        if ($depotKey) {
            $query->where('depot', $depotKey);
        }

        $rows = $query->get()->map(function ($row) {
            $montant = round((float) $row->total_montant, 2);
            $solde = round((float) $row->total_solde, 2);

            return [
                'fournisseur_id' => $row->fournisseur_id,
                'nom_fournisseur' => $row->nom_fournisseur,
                'nb_bons' => (int) $row->nb_bons,
                'montant' => $montant,
                'regle' => round($montant - $solde, 2),
                'solde' => $solde,
            ];
        });

        return view('fournisseurs.balance.index', [
            'rows' => $rows,
            'depotLabel' => UserAccess::depotLabel($user),
            'totalCmd' => (int) $rows->sum('nb_bons'),
            'totalMontant' => round($rows->sum('montant'), 2),
            'totalRegle' => round($rows->sum('regle'), 2),
            'totalSolde' => round($rows->sum('solde'), 2),
        ]);
    }
}
