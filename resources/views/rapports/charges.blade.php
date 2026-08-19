@extends('layouts.dashboard')

@section('title', $title)

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .filter select { padding:.55rem .75rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:800px; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .totals-bar { display:flex; justify-content:flex-end; margin-top:.85rem; color:var(--gold-light); font-weight:700; }
</style>
<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>{{ $title }}</h2>
        <div style="display:flex;gap:.65rem;flex-wrap:wrap;align-items:center;">
            @if (($depots ?? collect())->count() > 1)
            <form method="GET" class="filter">
                <select name="depot" onchange="this.form.submit()">
                    <option value="">Tous les dépôts</option>
                    @foreach ($depots as $key => $label)
                        <option value="{{ $key }}" @selected(($depot ?? null) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            @endif
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Date</th><th>Type</th><th>Libellé</th><th>Dépôt</th><th>Montant</th></tr></thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ optional($row['date'])->format('d/m/Y') }}</td>
                        <td>{{ $row['type'] }}</td>
                        <td>{{ $row['libelle'] }}</td>
                        <td>{{ $row['depot'] ?: '—' }}</td>
                        <td>{{ number_format($row['montant'], 2, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Aucune charge / dépense.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="totals-bar">Total : {{ number_format($total, 2, ',', ' ') }}</div>
</div>
@endsection
