@extends('layouts.dashboard')

@section('title', 'Commandes Dépôt')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .btn-sm { padding:.4rem .75rem; font-size:.78rem; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:680px; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .chip { display:inline-block; padding:.15rem .5rem; border-radius:999px; font-size:.72rem; font-weight:700; border:1px solid rgba(94,200,179,.3); color:var(--gold-light); }
    .chip.envoye { border-color:#60a5fa; color:#93c5fd; }
    .chip.converti { border-color:#c084fc; color:#d8b4fe; }
    .chip.expedie { border-color:#4ade80; color:#86efac; }
    .chip.brouillon { border-color:#94a3b8; color:#cbd5e1; }
    .chip.suspendu { border-color:#fbbf24; color:#fde68a; }
    .actions { display:flex; gap:.35rem; flex-wrap:wrap; justify-content:center; }
    .icon-btn { width:34px; height:34px; border-radius:9px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; }
    .icon-btn:hover { background:rgba(94,200,179,.18); }
    .icon-btn.danger:hover { color:#ff9a9a; }
    .icon-btn.warn:hover { color:#fde68a; }
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
    .line-row { display:grid; grid-template-columns:1fr 2fr 1fr auto; gap:.5rem; margin-bottom:.5rem; align-items:end; }
    .modal-footer { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1rem; }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; }
    .alert-success { background:rgba(20,100,60,.25); border:1px solid rgba(100,255,150,.35); color:#b4ffd0; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; }
    .notif-banner { background:rgba(59,130,246,.15); border:1px solid rgba(96,165,250,.45); border-radius:12px; padding:.85rem 1rem; margin-bottom:.85rem; }
    .notif-banner h4 { color:#93c5fd; font-size:.85rem; margin-bottom:.45rem; }
    .notif-item { font-size:.82rem; color:var(--text); padding:.25rem 0; display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
    .detail-table { width:100%; border-collapse:collapse; margin-top:.75rem; }
    .detail-table th, .detail-table td { padding:.45rem .5rem; border-bottom:1px solid rgba(94,200,179,.15); font-size:.82rem; text-align:center; }
    .side-panel-backdrop { position:fixed; inset:0; background:rgba(7,11,20,.55); z-index:210; display:none; }
    .side-panel-backdrop.open { display:block; }
    .side-panel {
        position:fixed; top:0; right:0; width:min(480px,100%); height:100%;
        background:linear-gradient(160deg,rgba(84,0,11,.98),rgba(45,0,6,.99));
        border-left:1px solid rgba(94,200,179,.35);
        z-index:220; transform:translateX(100%); transition:transform .28s ease;
        display:flex; flex-direction:column; padding:1.25rem 1.35rem; overflow:auto;
    }
    .side-panel.open { transform:translateX(0); }
    .side-panel h3 { font-family:'Fraunces', serif; color:var(--gold); margin-bottom:1rem; }
    .side-panel-footer { margin-top:auto; padding-top:1rem; display:flex; justify-content:flex-end; gap:.65rem; }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Commandes Dépôt</h2>
        <div class="toolbar-actions">
            @if (! $isCentral)
                <button type="button" class="btn btn-gold" onclick="openCreate()">Nouveau BN</button>
            @endif
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    @if (session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error">{{ $errors->first() }}</div>@endif

    @if ($isCentral && $notifications->isNotEmpty())
        <div class="notif-banner">
            <h4>Notifications — {{ $pendingCount }} commande(s) reçue(s)</h4>
            @foreach ($notifications as $notif)
                <div class="notif-item">
                    <span>
                        <strong>{{ $notif->numero }}</strong>
                        — {{ $depots[$notif->depot_demandeur] ?? $notif->depot_demandeur }}
                        ({{ $notif->date_commande?->format('d/m/Y') }})
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° BN</th>
                    <th>Dépôt</th>
                    <th>Quantité Total</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($commandes as $cmd)
                    @php $qteTotal = $cmd->qteTotale(); @endphp
                    <tr>
                        <td>{{ $cmd->date_commande?->format('d/m/Y') }}</td>
                        <td>{{ $cmd->numero }}</td>
                        <td>{{ $depots[$cmd->depot_demandeur] ?? $cmd->depot_demandeur }}</td>
                        <td>{{ number_format($qteTotal, 2, ',', ' ') }}</td>
                        <td><span class="chip {{ $cmd->statut }}">{{ $statuts[$cmd->statut] ?? $cmd->statut }}</span></td>
                        <td>
                            <div class="actions">
                                <button type="button" class="icon-btn" title="Voir" onclick='openView(@json($cmd))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <a href="{{ route('stock.commande_depot.print', $cmd) }}" target="_blank" class="icon-btn" title="Imprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                </a>
                                @if ($isCentral && in_array($cmd->statut, ['envoye', 'converti'], true))
                                    <button type="button" class="icon-btn" title="Convertir" onclick='openConvert(@json($cmd))'>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </button>
                                @endif
                                @if ($isCentral && in_array($cmd->statut, ['envoye', 'converti', 'brouillon'], true))
                                    <form method="POST" action="{{ route('stock.commande_depot.suspendre', $cmd) }}" onsubmit="return confirm('Suspendre cette commande ?');">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="icon-btn warn" title="Suspendre">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                                        </button>
                                    </form>
                                @endif
                                @if ($isCentral && in_array($cmd->statut, ['envoye', 'converti'], true))
                                    <form method="POST" action="{{ route('stock.commande_depot.expedier', $cmd) }}" onsubmit="return confirm('Expédier et créer le transfert ?');">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="icon-btn" title="Expédier">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                        </button>
                                    </form>
                                @endif
                                @if (! $isCentral && $cmd->statut === 'brouillon')
                                    <button type="button" class="icon-btn" title="Modifier" onclick='openEdit(@json($cmd))'>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('stock.commande_depot.envoyer', $cmd) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="icon-btn" title="Envoyer">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Aucune commande.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Voir --}}
<div class="modal-backdrop" id="viewModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="viewTitle">Détail commande</h3>
            <button type="button" class="icon-btn" onclick="closeView()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
        </div>
        <div id="viewMeta" style="font-size:.82rem;color:var(--text-muted);margin-bottom:.5rem;"></div>
        <table class="detail-table">
            <thead><tr><th>Réf</th><th>Désignation</th><th>Qté</th></tr></thead>
            <tbody id="viewLines"></tbody>
        </table>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeView()">Fermer</button>
        </div>
    </div>
</div>

@if ($isCentral)
{{-- Panneau Convertir → Bon de Charge --}}
<div class="side-panel-backdrop" id="convertBackdrop" onclick="closeConvert()"></div>
<aside class="side-panel" id="convertPanel">
    <h3>Convertir — Bon de Charge</h3>
    <form method="POST" id="convertForm">
        @csrf @method('PATCH')
        <div class="form-grid">
            <div class="field"><label>N° Bon De Charge</label><input type="text" id="conv_numero" readonly></div>
            <div class="field"><label>Date</label><input type="date" name="date_bon_charge" id="conv_date" required></div>
            <div class="field full"><label>Dépôt</label><input type="text" id="conv_depot" readonly></div>
        </div>
        <table class="detail-table">
            <thead><tr><th>Réf</th><th>Désignation</th><th>Qté</th></tr></thead>
            <tbody id="convLines"></tbody>
        </table>
        <div class="side-panel-footer">
            <button type="button" class="btn btn-ghost" onclick="closeConvert()">Fermer</button>
            <button type="submit" class="btn btn-gold">Valider</button>
        </div>
    </form>
</aside>
@endif

@if (! $isCentral)
<div class="modal-backdrop" id="modal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="modalTitle">Nouveau Bon</h3>
            <button type="button" class="icon-btn" onclick="closeModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('stock.commande_depot.store') }}" id="cmdForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="form-grid">
                <div class="field"><label>Date</label><input type="date" name="date_commande" id="field_date" value="{{ old('date_commande', now()->toDateString()) }}" required></div>
                <div class="field"><label>N° BN</label><input type="text" id="field_numero" value="{{ $nextNumero }}" readonly></div>
                <div class="field"><label>Dépôt</label>
                    <select name="depot_demandeur" id="field_depot" required @disabled($lockedDepot)>
                        @foreach ($regionalDepots as $key => $label)
                            <option value="{{ $key }}" @selected(old('depot_demandeur', $lockedDepot) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($lockedDepot)<input type="hidden" name="depot_demandeur" value="{{ $lockedDepot }}">@endif
                </div>
                <div class="field"><label>Fournisseur</label><input type="text" value="Dépôt DamioRif" readonly></div>
                <div class="field full"><label>Note</label><textarea name="note" id="field_note" rows="2">{{ old('note') }}</textarea></div>
            </div>
            <div class="lines">
                <div id="lines"></div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addLine()">+ Ligne</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Fermer</button>
                <button type="submit" name="envoyer" value="1" class="btn btn-gold">Envoyer</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
const depots = @json($depots);
const nextBonCharge = @json($nextNumeroBonCharge);

function openView(cmd) {
    document.getElementById('viewTitle').textContent = cmd.numero;
    document.getElementById('viewMeta').innerHTML =
        `Date : ${(cmd.date_commande || '').substring(0,10).split('-').reverse().join('/')} — Dépôt : ${depots[cmd.depot_demandeur] || cmd.depot_demandeur}`;
    const tbody = document.getElementById('viewLines');
    tbody.innerHTML = '';
    (cmd.lignes || []).forEach(l => {
        tbody.innerHTML += `<tr><td>${l.ref || '—'}</td><td>${l.designation}</td><td>${parseFloat(l.qte_demandee).toLocaleString('fr-FR',{minimumFractionDigits:2})}</td></tr>`;
    });
    document.getElementById('viewModal').classList.add('open');
}
function closeView(){ document.getElementById('viewModal').classList.remove('open'); }
document.getElementById('viewModal').addEventListener('click', e => { if (e.target.id === 'viewModal') closeView(); });

@if ($isCentral)
function openConvert(cmd) {
    const form = document.getElementById('convertForm');
    form.action = @json(url('/stock/commande-depot')) + '/' + cmd.id + '/convertir';
    document.getElementById('conv_numero').value = cmd.numero_bon_charge || nextBonCharge;
    document.getElementById('conv_date').value = (cmd.date_bon_charge || cmd.date_commande || '').substring(0, 10) || new Date().toISOString().substring(0,10);
    document.getElementById('conv_depot').value = depots[cmd.depot_demandeur] || cmd.depot_demandeur;
    const tbody = document.getElementById('convLines');
    tbody.innerHTML = '';
    (cmd.lignes || []).forEach(l => {
        tbody.innerHTML += `<tr><td>${l.ref || '—'}</td><td>${l.designation}</td><td>${parseFloat(l.qte_demandee).toLocaleString('fr-FR',{minimumFractionDigits:2})}</td></tr>`;
    });
    document.getElementById('convertBackdrop').classList.add('open');
    document.getElementById('convertPanel').classList.add('open');
}
function closeConvert(){
    document.getElementById('convertBackdrop').classList.remove('open');
    document.getElementById('convertPanel').classList.remove('open');
}
@endif

@if (! $isCentral)
const modal = document.getElementById('modal');
const form = document.getElementById('cmdForm');
let lineIdx = 0;
function openModal(){ modal.classList.add('open'); }
function closeModal(){ modal.classList.remove('open'); }
function addLine(data){
    const i = lineIdx++;
    const wrap = document.createElement('div');
    wrap.className = 'line-row';
    wrap.innerHTML = `
        <div class="field"><label>Réf</label><input type="text" name="lignes[${i}][ref]" value="${data?.ref ?? ''}"></div>
        <div class="field"><label>Désignation</label><input type="text" name="lignes[${i}][designation]" value="${data?.designation ?? ''}" required></div>
        <div class="field"><label>Qté</label><input type="number" step="0.001" min="0.001" name="lignes[${i}][qte_demandee]" value="${data?.qte_demandee ?? ''}" required></div>
        <button type="button" class="icon-btn danger" onclick="this.parentElement.remove()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    `;
    document.getElementById('lines').appendChild(wrap);
}
function openCreate(){
    document.getElementById('modalTitle').textContent = 'Nouveau Bon';
    document.getElementById('formMethod').value = 'POST';
    form.action = @json(route('stock.commande_depot.store'));
    document.getElementById('field_numero').value = @json($nextNumero);
    document.getElementById('field_date').value = @json(now()->toDateString());
    document.getElementById('field_note').value = '';
    document.getElementById('lines').innerHTML = '';
    lineIdx = 0; addLine(); openModal();
}
function openEdit(cmd){
    if (!cmd) return;
    document.getElementById('modalTitle').textContent = 'Modifier ' + cmd.numero;
    document.getElementById('formMethod').value = 'PUT';
    form.action = @json(url('/stock/commande-depot')) + '/' + cmd.id;
    document.getElementById('field_numero').value = cmd.numero;
    document.getElementById('field_date').value = (cmd.date_commande || '').substring(0, 10);
    document.getElementById('field_note').value = cmd.note || '';
    document.getElementById('lines').innerHTML = '';
    lineIdx = 0;
    (cmd.lignes || []).forEach(l => addLine({ ref: l.ref, designation: l.designation, qte_demandee: l.qte_demandee }));
    if (!cmd.lignes?.length) addLine();
    openModal();
}
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
@if(isset($errors) && $errors->any()) openCreate(); @endif
@endif
</script>
@endsection
