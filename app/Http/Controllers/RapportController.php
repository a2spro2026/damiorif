<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Models\BonVente;
use App\Models\Charge;
use App\Models\ReglementAchat;
use App\Models\ReglementVente;
use App\Support\Depots;
use App\Support\TypesReglement;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RapportController extends Controller
{
    public function releveFournisseurs(Request $request): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user) ?: $request->query('depot');

        $bons = BonAchat::query()->orderByDesc('date_bon');
        if ($depotKey) {
            $bons->where('depot', $depotKey);
        }

        $regs = ReglementAchat::query()->orderByDesc('date_reglement');

        return view('rapports.releve', [
            'title' => 'Relevés Compte Fournisseurs',
            'rows' => $this->mergeTimeline(
                $bons->get()->map(fn ($b) => [
                    'date' => $b->date_bon,
                    'piece' => $b->numero_bon,
                    'tiers' => $b->nom_fournisseur,
                    'libelle' => 'Bon d\'achat',
                    'debit' => (float) $b->montant,
                    'credit' => 0.0,
                    'solde' => (float) $b->solde,
                ]),
                $regs->get()->map(fn ($r) => [
                    'date' => $r->date_reglement,
                    'piece' => $r->numero,
                    'tiers' => $r->nom_fournisseur,
                    'libelle' => 'Règlement achat — '.$this->typeLabel($r->type_reglement),
                    'debit' => 0.0,
                    'credit' => (float) $r->montant,
                    'solde' => null,
                ]),
            ),
            'depots' => UserAccess::depotOptionsFor($user),
            'depot' => $depotKey,
        ]);
    }

    public function releveClients(Request $request): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user) ?: $request->query('depot');

        $bons = BonVente::query()->orderByDesc('date_bon');
        if ($depotKey) {
            $bons->where('depot', $depotKey);
        }

        $regs = ReglementVente::query()->orderByDesc('date_reglement');

        return view('rapports.releve', [
            'title' => 'Relevé Compte Clients',
            'rows' => $this->mergeTimeline(
                $bons->get()->map(fn ($b) => [
                    'date' => $b->date_bon,
                    'piece' => $b->numero_bon,
                    'tiers' => $b->nom_client,
                    'libelle' => 'Bon de vente',
                    'debit' => (float) $b->montant,
                    'credit' => 0.0,
                    'solde' => (float) $b->solde,
                ]),
                $regs->get()->map(fn ($r) => [
                    'date' => $r->date_reglement,
                    'piece' => $r->numero,
                    'tiers' => $r->nom_client,
                    'libelle' => 'Règlement vente — '.$this->typeLabel($r->type_reglement),
                    'debit' => 0.0,
                    'credit' => (float) $r->montant,
                    'solde' => null,
                ]),
            ),
            'depots' => UserAccess::depotOptionsFor($user),
            'depot' => $depotKey,
        ]);
    }

    public function releveCaisse(Request $request): View
    {
        return $this->releveReglements($request, 'especes', 'Relevés Compte Caisse');
    }

    public function releveTresorerie(Request $request): View
    {
        return $this->releveReglements($request, null, 'Relevé Compte Trésorerie', excludeEspeces: true);
    }

    public function releveDepots(Request $request): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user) ?: $request->query('depot');

        $achats = BonAchat::query()
            ->select('depot', DB::raw('COALESCE(SUM(montant),0) as total'))
            ->when($depotKey, fn ($q) => $q->where('depot', $depotKey))
            ->groupBy('depot')
            ->pluck('total', 'depot');

        $ventes = BonVente::query()
            ->select('depot', DB::raw('COALESCE(SUM(montant),0) as total'))
            ->when($depotKey, fn ($q) => $q->where('depot', $depotKey))
            ->groupBy('depot')
            ->pluck('total', 'depot');

        $charges = Charge::query()
            ->select('depot', 'type', DB::raw('COALESCE(SUM(montant),0) as total'))
            ->when($depotKey, fn ($q) => $q->where('depot', $depotKey))
            ->groupBy('depot', 'type')
            ->get();

        $rows = collect(Depots::options())
            ->when($depotKey, fn ($c) => $c->only([(string) $depotKey]))
            ->map(function ($label, $key) use ($achats, $ventes, $charges) {
                $charge = (float) $charges->where('depot', $key)->where('type', 'charge')->sum('total');
                $depense = (float) $charges->where('depot', $key)->where('type', 'depense')->sum('total');

                return [
                    'depot' => $label,
                    'achats' => round((float) ($achats[$key] ?? 0), 2),
                    'ventes' => round((float) ($ventes[$key] ?? 0), 2),
                    'charges' => round($charge, 2),
                    'depenses' => round($depense, 2),
                ];
            })
            ->values();

        return view('rapports.depots', [
            'title' => 'Relevé Compte Depots',
            'rows' => $rows,
            'depots' => UserAccess::depotOptionsFor($user),
            'depot' => $depotKey,
        ]);
    }

    public function releveCharges(Request $request): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user) ?: $request->query('depot');

        $query = Charge::query()->orderByDesc('date_charge');
        if ($depotKey) {
            $query->where('depot', $depotKey);
        }

        $rows = $query->get()->map(fn (Charge $c) => [
            'date' => $c->date_charge,
            'type' => $c->type === 'depense' ? 'Dépense' : 'Charge',
            'libelle' => $c->libelle ?: '—',
            'depot' => Depots::options()[$c->depot] ?? $c->depot,
            'montant' => (float) $c->montant,
        ]);

        return view('rapports.charges', [
            'title' => 'Relevés Compte Charges et Dépenses',
            'rows' => $rows,
            'total' => round($rows->sum('montant'), 2),
            'depots' => UserAccess::depotOptionsFor($user),
            'depot' => $depotKey,
        ]);
    }

    private function releveReglements(Request $request, ?string $typeKey, string $title, bool $excludeEspeces = false): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user) ?: $request->query('depot');

        $ventes = ReglementVente::query()->orderByDesc('date_reglement');
        $achats = ReglementAchat::query()->orderByDesc('date_reglement');

        if ($typeKey) {
            $ventes->where('type_reglement', $typeKey);
            $achats->where('type_reglement', $typeKey);
        } elseif ($excludeEspeces) {
            $ventes->where(function ($q) {
                $q->whereNull('type_reglement')->orWhere('type_reglement', '!=', 'especes');
            });
            $achats->where(function ($q) {
                $q->whereNull('type_reglement')->orWhere('type_reglement', '!=', 'especes');
            });
        }

        $rows = $this->mergeTimeline(
            $ventes->get()->map(fn ($r) => [
                'date' => $r->date_reglement,
                'piece' => $r->numero,
                'tiers' => $r->nom_client,
                'libelle' => 'Encaissement — '.$this->typeLabel($r->type_reglement),
                'debit' => 0.0,
                'credit' => (float) $r->montant,
                'solde' => null,
            ]),
            $achats->get()->map(fn ($r) => [
                'date' => $r->date_reglement,
                'piece' => $r->numero,
                'tiers' => $r->nom_fournisseur,
                'libelle' => 'Décaissement — '.$this->typeLabel($r->type_reglement),
                'debit' => (float) $r->montant,
                'credit' => 0.0,
                'solde' => null,
            ]),
        );

        return view('rapports.releve', [
            'title' => $title,
            'rows' => $rows,
            'depots' => UserAccess::depotOptionsFor($user),
            'depot' => $depotKey,
        ]);
    }

    private function typeLabel(?string $type): string
    {
        return TypesReglement::options()[$type] ?? ($type ?: '—');
    }

    private function mergeTimeline($a, $b)
    {
        return collect($a)->merge(collect($b))
            ->sortByDesc(fn ($row) => optional($row['date'])->format('Y-m-d').'-'.$row['piece'])
            ->values();
    }
}
