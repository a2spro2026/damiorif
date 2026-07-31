@extends('layouts.dashboard')

@section('title', 'Balance Clients')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--gold); letter-spacing:.04em; }
    .page-meta { font-size:.78rem; color:rgba(255,255,255,.45); margin-top:.2rem; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(201,164,92,.35); }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(201,164,92,.18); background:rgba(0,0,0,.22); }
    .data-table { width:100%; border-collapse:collapse; min-width:820px; }
    .totals-bar { display:flex; justify-content:flex-end; gap:1.5rem; margin-top:.85rem; padding-top:.75rem; border-top:1px solid rgba(201,164,92,.18); color:var(--gold-light); font-weight:700; flex-wrap:wrap; }
    .empty-row td { text-align:center; color:rgba(255,255,255,.45); padding:2rem; }
    .solde-pos { color:#fecaca; font-weight:700; }
    .solde-zero { color:#bbf7d0; font-weight:700; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <div>
            <h2>Balance Clients</h2>
            <div class="page-meta">
                @if ($depotLabel)
                    Dépôt : {{ $depotLabel }} —
                @endif
                {{ $rows->count() }} client{{ $rows->count() > 1 ? 's' : '' }}
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Nb Bons</th>
                    <th>Montant</th>
                    <th>Réglé</th>
                    <th>Solde</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['nom_client'] }}</td>
                        <td>{{ $row['nb_bons'] }}</td>
                        <td>{{ number_format($row['montant'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($row['regle'], 2, ',', ' ') }}</td>
                        <td class="{{ $row['solde'] > 0 ? 'solde-pos' : 'solde-zero' }}">
                            {{ number_format($row['solde'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Aucune vente enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="totals-bar">
        <span>Montant : {{ number_format($totalMontant, 2, ',', ' ') }}</span>
        <span>Réglé : {{ number_format($totalRegle, 2, ',', ' ') }}</span>
        <span>Solde : {{ number_format($totalSolde, 2, ',', ' ') }}</span>
    </div>
</div>
@endsection
