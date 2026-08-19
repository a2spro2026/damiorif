@extends('layouts.dashboard')

@section('title', $depotLabel)

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .page-meta { font-size:.78rem; color:var(--text-muted); margin-top:.2rem; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:720px; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <div>
            <h2>{{ $depotLabel }}</h2>
            <div class="page-meta">Achats saisis vers ce dépôt</div>
        </div>
        <div style="display:flex;gap:.65rem;flex-wrap:wrap;">
            <a href="{{ route('stock.mouvement') }}" class="btn btn-ghost">Mouvements</a>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    @include('partials.kpi-grid', ['cards' => [
        ['label' => 'Lignes achats', 'value' => $achatLignes->count(), 'integer' => true],
        ['label' => 'Qté achats', 'value' => $totalAchatsQte, 'unit' => 'QTE'],
        ['label' => 'Montant achats', 'value' => $totalAchatsMontant, 'unit' => 'MAD'],
    ]])

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° Bon</th>
                    <th>Fournisseur</th>
                    <th>Réf</th>
                    <th>Désignation</th>
                    <th>Qté</th>
                    <th>P/U</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($achatLignes as $ligne)
                    <tr>
                        <td>{{ $ligne['date']?->format('d/m/Y') }}</td>
                        <td>{{ $ligne['numero_bon'] }}</td>
                        <td>{{ $ligne['fournisseur'] }}</td>
                        <td>{{ $ligne['ref'] }}</td>
                        <td>{{ $ligne['designation'] }}</td>
                        <td>{{ number_format($ligne['qte'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($ligne['prix_unitaire'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($ligne['montant'], 2, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="8">Aucun achat saisi pour ce dépôt.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
