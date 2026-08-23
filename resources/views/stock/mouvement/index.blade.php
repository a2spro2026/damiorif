@extends('layouts.dashboard')

@section('title', 'Mouvement Stock')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; align-items:center; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .filter-bar { display:flex; gap:.65rem; flex-wrap:wrap; align-items:center; }
    .filter-bar select { padding:.6rem .75rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); font-family:inherit; font-size:.85rem; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:1100px; }
    .data-table th, .data-table td { padding:.55rem .45rem; font-size:.82rem; text-align:center; }
    .data-table th:first-child, .data-table td:first-child,
    .data-table th:nth-child(2), .data-table td:nth-child(2) { text-align:left; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .qte-pos { color:#bbf7d0; font-weight:700; }
    .qte-muted { color:var(--text-muted); }
    .col-stock { background:rgba(94,200,179,.08); font-weight:700; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Mouvement Stock — {{ $depotLabel }} ({{ $selectedYear }})</h2>
        <div class="toolbar-actions">
            <form method="GET" class="filter-bar">
                @if (count($depotOptions) > 1)
                    <select name="depot" onchange="this.form.submit()">
                        @foreach ($depotOptions as $key => $label)
                            <option value="{{ $key }}" @selected($selectedDepot === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="annee" onchange="this.form.submit()">
                    @foreach ($yearOptions as $year)
                        <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Réf</th>
                    <th>Désignation</th>
                    @foreach ($monthLabels as $label)
                        <th>{{ $label }}</th>
                    @endforeach
                    <th class="col-stock">Stock Actuel</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($releveRows as $row)
                    <tr>
                        <td>{{ $row['ref'] }}</td>
                        <td>{{ $row['designation'] }}</td>
                        @for ($m = 1; $m <= 12; $m++)
                            @php $v = $row['ventes'][$m] ?? 0; @endphp
                            <td class="{{ $v > 0 ? '' : 'qte-muted' }}">{{ $v > 0 ? number_format($v, 2, ',', ' ') : '—' }}</td>
                        @endfor
                        <td class="col-stock {{ $row['qte_stock'] > 0 ? 'qte-pos' : '' }}">
                            {{ number_format($row['qte_stock'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="15">Aucune vente ni stock pour ce dépôt.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
