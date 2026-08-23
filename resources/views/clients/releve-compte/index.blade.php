@extends('layouts.dashboard')

@section('title', $title)

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); letter-spacing:.04em; }
    .page-meta { font-size:.78rem; color:var(--text-muted); margin-top:.2rem; }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; align-items:center; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .filter-bar { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:.65rem; margin-bottom:.85rem; }
    .filter-bar select { width:100%; padding:.6rem .75rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); font-family:inherit; font-size:.85rem; outline:none; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .amt { text-align:right; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <div>
            <h2>{{ $title }}</h2>
            @if ($depotLabel ?? null)
                <div class="page-meta">Dépôt : {{ $depotLabel }}</div>
            @endif
        </div>
        <div class="toolbar-actions">
            <a href="{{ route($printRoute, request()->query()) }}" target="_blank" class="btn btn-gold">Imprimer</a>
            <a href="{{ route($closeRoute) }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    @include('partials.kpi-grid', ['cards' => [
        ['label' => 'Total Achats', 'value' => $totalAchats, 'unit' => 'MAD'],
        ['label' => 'Total Payé', 'value' => $totalPaye, 'unit' => 'MAD'],
        ['label' => 'Total Solde', 'value' => $totalSolde, 'unit' => 'MAD'],
    ]])

    <form method="GET" class="filter-bar">
        <select name="mois" onchange="this.form.submit()">
            <option value="">— Tous les mois —</option>
            @foreach ($monthOptions as $opt)
                <option value="{{ $opt['value'] }}" @selected(($selectedMois ?? '') === $opt['value'])>{{ $opt['label'] }}</option>
            @endforeach
        </select>
        <select name="{{ $tiersField }}" onchange="this.form.submit()">
            <option value="">— Tous les clients —</option>
            @foreach ($tiersList as $item)
                <option value="{{ $item->id }}" @selected(($selectedTiersId ?? null) === $item->id)>{{ $item->nom_client }}</option>
            @endforeach
        </select>
    </form>

    @include('partials.releve-compte-table', ['rows' => $rows, 'tiersLabel' => $tiersLabel])
</div>
@endsection
