@extends('layouts.dashboard')

@section('title', 'Manques régionaux')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .page-meta { font-size:.78rem; color:var(--text-muted); margin-top:.2rem; max-width:42rem; line-height:1.45; }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; align-items:center; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .filter-bar { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:.65rem; margin-bottom:.85rem; }
    .filter-bar select { width:100%; padding:.6rem .75rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); font-family:inherit; font-size:.85rem; outline:none; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:900px; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .manque-val { color:#fecaca; font-weight:700; }
    .depot-chip { display:inline-block; padding:.12rem .45rem; border-radius:999px; font-size:.68rem; font-weight:700; background:rgba(94,200,179,.12); color:var(--gold-light); white-space:nowrap; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <div>
            <h2>Manques régionaux</h2>
            <div class="page-meta">
                @if ($isCentralView)
                    Vue centrale DamioRif — produits vendus en dépôt régional dont la qté vendue dépasse le stock actuel.
                @else
                    Manques calculés pour votre dépôt (ventes − stock actuel).
                @endif
            </div>
        </div>
        <div class="toolbar-actions">
            <a href="{{ route('stock.depot', ['depot' => 'damiorif']) }}" class="btn btn-ghost">Dépôt central</a>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    @include('partials.kpi-grid', ['cards' => [
        ['label' => 'Lignes en manque', 'value' => $totalLignes, 'integer' => true],
        ['label' => 'Qté totale manquante', 'value' => $totalManque, 'unit' => 'QTE'],
    ]])

    <form method="GET" class="filter-bar">
        @if (count($depotOptions) > 1)
            <select name="depot" onchange="this.form.submit()">
                <option value="">— Tous les dépôts régionaux —</option>
                @foreach ($depotOptions as $key => $label)
                    <option value="{{ $key }}" @selected(($selectedDepot ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        @endif
        <select name="mois" onchange="this.form.submit()">
            @foreach ($monthOptions as $opt)
                <option value="{{ $opt['value'] }}" @selected(($selectedMois ?? '') === $opt['value'])>{{ $opt['label'] }}</option>
            @endforeach
        </select>
    </form>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Dépôt</th>
                    <th>Réf</th>
                    <th>Désignation</th>
                    <th>Qté vendue</th>
                    <th>Stock actuel</th>
                    <th>Manque</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td><span class="depot-chip">{{ $row['depot_label'] }}</span></td>
                        <td>{{ $row['ref'] }}</td>
                        <td>{{ $row['designation'] }}</td>
                        <td>{{ number_format($row['qte_vendue'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($row['qte_stock'], 2, ',', ' ') }}</td>
                        <td class="manque-val">{{ number_format($row['manque'], 2, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Aucun manque pour ces critères.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
