@extends('layouts.dashboard')

@section('title', 'Ville')

@section('content')
<style>
    .page-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        gap: 1rem; margin-bottom: 0.85rem; flex-wrap: wrap;
    }
    .page-toolbar h2 {
        font-family: 'Fraunces', serif; font-size: 1.35rem;
        color: var(--gold); letter-spacing: 0.04em;
    }
    .btn {
        display: inline-flex; align-items: center; gap: 0.45rem;
        padding: 0.65rem 1.15rem; border-radius: 10px;
        font-family: inherit; font-size: 0.88rem; font-weight: 700;
        cursor: pointer; border: 1px solid transparent; text-decoration: none;
    }
    .btn-ghost {
        background: rgba(0,0,0,0.25); color: var(--gold-light);
        border-color: rgba(94,200,179,0.35);
    }
    .btn-ghost:hover { background: rgba(94,200,179,0.12); border-color: var(--gold); }
    .table-wrap {
        overflow-x: auto; border-radius: 14px;
        border: 1px solid rgba(94,200,179,0.18); background:var(--surface);
    }
    .data-table { width: 100%; border-collapse: collapse; }
    .empty-row td { text-align: center; color:var(--text-muted); padding: 2rem; }
    .chip {
        display: inline-block; padding: 0.2rem 0.55rem; border-radius: 999px;
        font-size: 0.75rem; background: rgba(94,200,179,0.12);
        border: 1px solid rgba(94,200,179,0.28); color: var(--gold-light);
    }
    .page-note {
        color:var(--text-muted); font-size: 0.82rem; margin-bottom: 0.85rem;
    }
    .fiche-page { padding: 0.75rem 1.25rem 1.25rem !important; margin-top: -0.5rem; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Ville</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
    </div>

    <p class="page-note">
        Villes saisies automatiquement depuis les fiches Fournisseurs et Clients.
    </p>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ville</th>
                    <th>Fournisseurs</th>
                    <th>Clients</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($villes as $ville)
                    <tr>
                        <td>{{ $ville['id'] }}</td>
                        <td><strong>{{ $ville['nom'] }}</strong></td>
                        <td><span class="chip">{{ $ville['fournisseurs'] }}</span></td>
                        <td><span class="chip">{{ $ville['clients'] }}</span></td>
                        <td>{{ $ville['created_at']?->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">Aucune ville enregistrée. Ajoutez une ville dans une fiche Fournisseur ou Client.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
