@extends('layouts.dashboard')

@section('title', 'Unités de mesure')

@section('content')
<style>
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--gold); letter-spacing:.04em; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; text-decoration:none; border:1px solid transparent; }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(201,164,92,.35); }
    .btn-ghost:hover { background:rgba(201,164,92,.12); border-color:var(--gold); }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(201,164,92,.18); background:rgba(0,0,0,.22); }
    .data-table { width:100%; border-collapse:collapse; }
    .empty-row td { text-align:center; color:rgba(255,255,255,.45); padding:2rem; }
    .chip { display:inline-block; padding:.2rem .55rem; border-radius:999px; font-size:.75rem; background:rgba(201,164,92,.12); border:1px solid rgba(201,164,92,.28); color:var(--gold-light); }
    .page-note { color:rgba(255,255,255,.45); font-size:.82rem; margin-bottom:.85rem; }
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Unités de mesure</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
    </div>
    <p class="page-note">Unités saisies automatiquement depuis la Fiche Produit (Stock).</p>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Unité</th>
                    <th>Produits</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($unites as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td><strong>{{ $item['nom'] }}</strong></td>
                        <td><span class="chip">{{ $item['produits'] }}</span></td>
                        <td>{{ $item['created_at']?->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="4">Aucune unité enregistrée. Ajoutez une unité dans Stock → Fiche Produit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
