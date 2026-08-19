@extends('layouts.dashboard')

@section('title', 'Banque')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .action-btns { display:flex; gap:.35rem; }
    .icon-btn { width:34px; height:34px; border-radius:9px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
    .icon-btn.danger:hover { color:#ff9a9a; }
    .icon-btn svg { width:16px; height:16px; }
    .modal-backdrop { position:fixed; inset:0; background:rgba(7,11,20,.72); backdrop-filter:blur(6px); z-index:200; display:none; align-items:center; justify-content:center; padding:1rem; }
    .modal-backdrop.open { display:flex; }
    .modal-sheet { width:min(480px,100%); background:linear-gradient(160deg,rgba(84,0,11,.97),rgba(45,0,6,.98)); border:1px solid rgba(94,200,179,.35); border-radius:18px; padding:1.5rem; }
    .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .modal-header h3 { font-family:'Fraunces', serif; color:var(--gold); font-size:1.2rem; }
    .field label { display:block; font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; color:var(--gold-light); margin-bottom:.4rem; }
    .field input { width:100%; padding:.7rem .85rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); }
    .modal-footer { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.1rem; }
    .page-note { color:var(--text-muted); font-size:.82rem; margin-bottom:.85rem; }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Banque</h2>
        <div class="toolbar-actions">
            <button type="button" class="btn btn-gold" onclick="openCreate()">Ajouter</button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>
    <p class="page-note">Banques synchronisées aussi depuis les fiches Fournisseurs / Clients.</p>
    @if ($errors->any())<div class="alert-error">{{ $errors->first() }}</div>@endif

    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>ID</th><th>Banque</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
                @forelse ($banques as $b)
                    <tr>
                        <td>{{ $b->id }}</td>
                        <td><strong>{{ $b->nom }}</strong></td>
                        <td>{{ $b->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <div class="action-btns">
                                <button type="button" class="icon-btn" onclick='openEdit(@json($b))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('configuration.parametres.banque.destroy', $b) }}" onsubmit="return confirm('Supprimer ?');" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="icon-btn danger" type="submit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="4">Aucune banque.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop" id="modal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="modalTitle">Ajouter une banque</h3>
            <button type="button" class="icon-btn" onclick="closeModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" id="form">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="field"><label>Nom</label><input type="text" name="nom" id="field_nom" required></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Fermer</button>
                <button type="submit" class="btn btn-gold">Valider</button>
            </div>
        </form>
    </div>
</div>
<script>
    const modal = document.getElementById('modal');
    const form = document.getElementById('form');
    const storeUrl = @json(route('configuration.parametres.banque.store'));
    const updateBase = @json(url('/configuration/parametres/banque'));
    function openModal(){ modal.classList.add('open'); }
    function closeModal(){ modal.classList.remove('open'); }
    function openCreate(){ document.getElementById('modalTitle').textContent='Ajouter une banque'; form.action=storeUrl; document.getElementById('formMethod').value='POST'; document.getElementById('field_nom').value=''; openModal(); }
    function openEdit(b){ document.getElementById('modalTitle').textContent='Modifier banque'; form.action=updateBase+'/'+b.id; document.getElementById('formMethod').value='PUT'; document.getElementById('field_nom').value=b.nom||''; openModal(); }
    modal.addEventListener('click', e => { if (e.target===modal) closeModal(); });
</script>
@endsection
