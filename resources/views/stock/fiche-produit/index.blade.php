@extends('layouts.dashboard')

@section('title', 'Fiche Produit')

@section('content')
<style>
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); letter-spacing:.04em; }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; transition:all .2s ease; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-size:.9rem; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:700px; }
    .action-btns { display:flex; gap:.35rem; }
    .icon-btn { width:34px; height:34px; border-radius:9px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
    .icon-btn.danger:hover { color:#ff9a9a; border-color:rgba(255,100,100,.5); }
    .icon-btn svg { width:16px; height:16px; }
    .modal-backdrop { position:fixed; inset:0; background:rgba(7,11,20,.72); backdrop-filter:blur(6px); z-index:200; display:none; align-items:center; justify-content:center; padding:1rem; }
    .modal-backdrop.open { display:flex; }
    .modal-sheet { width:min(560px,100%); background:linear-gradient(160deg,rgba(84,0,11,.97),rgba(45,0,6,.98)); border:1px solid rgba(94,200,179,.35); border-radius:18px; padding:1.5rem; box-shadow:0 20px 60px rgba(0,0,0,.55); }
    .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; padding-bottom:.85rem; border-bottom:1px solid rgba(94,200,179,.2); }
    .modal-header h3 { font-family:'Fraunces', serif; color:var(--gold); font-size:1.25rem; }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.9rem 1rem; }
    .form-grid .full { grid-column:1/-1; }
    .field label { display:block; font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; color:var(--gold-light); margin-bottom:.4rem; font-weight:600; }
    .field input { width:100%; padding:.7rem .85rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); font-family:inherit; font-size:.92rem; outline:none; }
    .field input:disabled,.field input[readonly] { opacity:.7; }
    .modal-footer { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.25rem; padding-top:1rem; border-top:1px solid rgba(94,200,179,.18); }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Fiche Produit</h2>
        <div class="toolbar-actions">
            <button type="button" class="btn btn-gold" onclick="openCreateModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Ajouter
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Réf Produit</th>
                    <th>Nom Produit</th>
                    <th>Unité</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($produits as $p)
                    <tr>
                        <td>{{ $p->date_fiche?->format('d/m/Y') ?? $p->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $p->ref_produit }}</td>
                        <td>{{ $p->nom_produit }}</td>
                        <td>{{ $p->unite ?: '—' }}</td>
                        <td>
                            <div class="action-btns">
                                <button type="button" class="icon-btn" title="Voir" onclick='openViewModal(@json($p))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button type="button" class="icon-btn" title="Modifier" onclick='openEditModal(@json($p))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('stock.fiche_produit.destroy', $p) }}" onsubmit="return confirm('Supprimer ce produit ?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn danger" title="Supprimer">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Aucun produit enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop" id="prdModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="modalTitle">Ajouter un produit</h3>
            <button type="button" class="icon-btn" onclick="closeModal()" title="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="prdForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="form-grid">
                <div class="field">
                    <label>Date</label>
                    <input type="text" id="field_date" value="{{ now()->format('d/m/Y') }}" readonly>
                </div>
                <div class="field">
                    <label>Réf Produit</label>
                    <input type="text" id="field_ref" value="{{ $nextRef }}" readonly>
                </div>
                <div class="field full">
                    <label for="field_nom">Nom Produit</label>
                    <input type="text" name="nom_produit" id="field_nom" required>
                </div>
                <div class="field full">
                    <label for="field_unite">Unité de mesure</label>
                    <input type="text" name="unite" id="field_unite" placeholder="ex: Kg, Sac, Carton…">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Fermer</button>
                <button type="submit" class="btn btn-gold" id="submitBtn">Valider</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('prdModal');
    const form = document.getElementById('prdForm');
    const storeUrl = @json(route('stock.fiche_produit.store'));
    const updateBase = @json(url('/stock/fiche-produit'));
    const nextRef = @json($nextRef);
    const today = @json(now()->format('d/m/Y'));

    function openModal() { modal.classList.add('open'); }
    function closeModal() { modal.classList.remove('open'); setFormReadonly(false); }
    function setFormReadonly(readonly) {
        form.querySelectorAll('input').forEach(el => {
            if (el.id === 'field_date' || el.id === 'field_ref' || el.type === 'hidden') return;
            el.disabled = readonly;
        });
        document.getElementById('submitBtn').style.display = readonly ? 'none' : 'inline-flex';
    }
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Ajouter un produit';
        form.action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_date').value = today;
        document.getElementById('field_ref').value = nextRef;
        document.getElementById('field_nom').value = '';
        document.getElementById('field_unite').value = '';
        setFormReadonly(false);
        openModal();
    }
    function fillForm(p) {
        const d = p.date_fiche || p.created_at;
        document.getElementById('field_date').value = d ? new Date(d).toLocaleDateString('fr-FR') : '—';
        document.getElementById('field_ref').value = p.ref_produit || '';
        document.getElementById('field_nom').value = p.nom_produit || '';
        document.getElementById('field_unite').value = p.unite || '';
    }
    function openEditModal(p) {
        document.getElementById('modalTitle').textContent = 'Modifier produit';
        form.action = updateBase + '/' + p.id;
        document.getElementById('formMethod').value = 'PUT';
        fillForm(p); setFormReadonly(false); openModal();
    }
    function openViewModal(p) {
        document.getElementById('modalTitle').textContent = 'Détail produit';
        form.action = '#'; fillForm(p); setFormReadonly(true); openModal();
    }
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    @if ($errors->any()) openCreateModal(); @endif
</script>
@endsection
