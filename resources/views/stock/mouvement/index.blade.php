@extends('layouts.dashboard')

@section('title', 'Mouvement Stock')

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
    .data-table { width:100%; border-collapse:collapse; min-width:900px; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .chip { display:inline-block; padding:.15rem .5rem; border-radius:999px; font-size:.75rem; border:1px solid rgba(94,200,179,.3); color:var(--gold-light); }
    .icon-btn { width:34px; height:34px; border-radius:9px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
    .icon-btn.danger:hover { color:#ff9a9a; }
    .icon-btn svg { width:16px; height:16px; }
    .modal-backdrop { position:fixed; inset:0; background:rgba(7,11,20,.72); z-index:200; display:none; align-items:center; justify-content:center; padding:1rem; overflow:auto; }
    .modal-backdrop.open { display:flex; }
    .modal-sheet { width:min(760px,100%); background:linear-gradient(160deg,rgba(84,0,11,.97),rgba(45,0,6,.98)); border:1px solid rgba(94,200,179,.35); border-radius:18px; padding:1.5rem; margin:auto; }
    .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .modal-header h3 { font-family:'Fraunces', serif; color:var(--gold); }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.85rem; }
    .form-grid .full { grid-column:1/-1; }
    .field label { display:block; font-size:.72rem; text-transform:uppercase; color:var(--gold-light); margin-bottom:.35rem; }
    .field input,.field select,.field textarea { width:100%; padding:.65rem .8rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); }
    .lines { margin-top:1rem; border-top:1px solid rgba(94,200,179,.2); padding-top:.85rem; }
    .line-row { display:grid; grid-template-columns:2fr 1fr auto; gap:.5rem; margin-bottom:.5rem; align-items:end; }
    .modal-footer { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1rem; }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Mouvement Stock</h2>
        <div class="toolbar-actions">
            <button type="button" class="btn btn-gold" onclick="openCreate()">Ajouter</button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>
    @if ($errors->any())<div class="alert-error">{{ $errors->first() }}</div>@endif

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N°</th>
                    <th>Type</th>
                    <th>Dépôt</th>
                    <th>Destination</th>
                    <th>Lignes</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mouvements as $m)
                    <tr>
                        <td>{{ $m->date_mouvement?->format('d/m/Y') }}</td>
                        <td>{{ $m->numero }}</td>
                        <td><span class="chip">{{ $types[$m->type] ?? $m->type }}</span></td>
                        <td>{{ $depots[$m->depot] ?? $m->depot }}</td>
                        <td>{{ $m->depot_destination ? ($depots[$m->depot_destination] ?? $m->depot_destination) : '—' }}</td>
                        <td>{{ $m->lignes->count() }}</td>
                        <td>
                            <form method="POST" action="{{ route('stock.mouvement.destroy', $m) }}" onsubmit="return confirm('Supprimer ce mouvement ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn danger"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Aucun mouvement.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop" id="modal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3>Nouveau mouvement</h3>
            <button type="button" class="icon-btn" onclick="closeModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('stock.mouvement.store') }}" id="mvtForm">
            @csrf
            <div class="form-grid">
                <div class="field"><label>Date</label><input type="date" name="date_mouvement" value="{{ now()->toDateString() }}" required></div>
                <div class="field"><label>N°</label><input type="text" value="{{ $nextNumero }}" readonly></div>
                <div class="field"><label>Type</label>
                    <select name="type" id="field_type" required onchange="toggleDest()">
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label>Dépôt</label>
                    <select name="depot" required @disabled($lockedDepot)>
                        @foreach ($depots as $key => $label)
                            <option value="{{ $key }}" @selected($lockedDepot === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($lockedDepot)<input type="hidden" name="depot" value="{{ $lockedDepot }}">@endif
                </div>
                <div class="field" id="destField" style="display:none;"><label>Dépôt destination</label>
                    <select name="depot_destination" id="field_dest">
                        <option value="">—</option>
                        @foreach ($depots as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field full"><label>Note</label><textarea name="note" rows="2"></textarea></div>
            </div>
            <div class="lines">
                <div id="lines"></div>
                <button type="button" class="btn btn-ghost" onclick="addLine()">+ Ligne</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Fermer</button>
                <button type="submit" class="btn btn-gold">Valider</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('modal');
const produits = @json($produits->map(fn($p)=>['id'=>$p->id,'label'=>$p->ref_produit.' — '.$p->nom_produit]));
let lineIdx = 0;
function openModal(){ modal.classList.add('open'); }
function closeModal(){ modal.classList.remove('open'); }
function toggleDest(){
    const t = document.getElementById('field_type').value;
    document.getElementById('destField').style.display = t === 'transfert' ? 'block' : 'none';
}
function addLine(){
    const i = lineIdx++;
    const opts = produits.map(p => `<option value="${p.id}">${p.label}</option>`).join('');
    const wrap = document.createElement('div');
    wrap.className = 'line-row';
    wrap.innerHTML = `
        <div class="field"><label>Produit</label>
            <select name="lignes[${i}][produit_id]" required><option value="">Choisir…</option>${opts}</select>
        </div>
        <div class="field"><label>Quantité</label><input type="number" step="0.001" min="0.001" name="lignes[${i}][quantite]" required></div>
        <button type="button" class="icon-btn danger" onclick="this.parentElement.remove()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    `;
    document.getElementById('lines').appendChild(wrap);
}
function openCreate(){
    document.getElementById('lines').innerHTML = '';
    lineIdx = 0;
    addLine();
    toggleDest();
    openModal();
}
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
@if ($errors->any()) openCreate(); @endif
</script>
@endsection
