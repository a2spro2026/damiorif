@extends('layouts.dashboard')

@section('title', 'Réglements')

@section('content')
<style>
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); letter-spacing:.04em; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; text-decoration:none; border:1px solid transparent; }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .btn-ghost:hover { background:rgba(94,200,179,.12); border-color:var(--gold); }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .chip { display:inline-block; padding:.2rem .55rem; border-radius:999px; font-size:.75rem; background:rgba(94,200,179,.12); border:1px solid rgba(94,200,179,.28); color:var(--gold-light); }
    .page-note { color:var(--text-muted); font-size:.82rem; margin-bottom:.85rem; }
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Réglements</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
    </div>
    <p class="page-note">Types de règlement saisis automatiquement depuis les fiches Fournisseurs et Clients.</p>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type Régl</th>
                    <th>Fournisseurs</th>
                    <th>Clients</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reglements as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td><strong>{{ $item['nom'] }}</strong></td>
                        <td><span class="chip">{{ $item['fournisseurs'] }}</span></td>
                        <td><span class="chip">{{ $item['clients'] }}</span></td>
                        <td>{{ $item['created_at']?->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Aucun règlement enregistré. Choisissez un Type Régl dans une fiche Fournisseur ou Client.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
