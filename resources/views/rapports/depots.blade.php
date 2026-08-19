@extends('layouts.dashboard')

@section('title', $title)

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:800px; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
</style>
<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>{{ $title }}</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Dépôt</th>
                    <th>Achats</th>
                    <th>Ventes</th>
                    <th>Charges</th>
                    <th>Dépenses</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['depot'] }}</td>
                        <td>{{ number_format($row['achats'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($row['ventes'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($row['charges'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($row['depenses'], 2, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Aucune donnée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
