@extends('layouts.dashboard')

@section('title', 'Alimenter Dépôt')

@section('content')
<style>
    .fiche-page {
        padding:.75rem 1.25rem 1.25rem !important;
        margin-top:-.5rem;
        display:flex;
        flex-direction:column;
        height:calc(100vh - 5.5rem);
        min-height:420px;
        overflow:hidden;
    }
    .alimenter-top { flex:0 0 auto; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.75rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .btn-sm { padding:.45rem .75rem; font-size:.8rem; }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:.75rem; font-size:.9rem; }
    .alert-ok { background:rgba(20,100,60,.25); border:1px solid rgba(100,255,160,.35); color:#b4ffd0; padding:.75rem 1rem; border-radius:10px; margin-bottom:.75rem; font-size:.9rem; }

    .form-shell {
        flex:1 1 auto;
        min-height:0;
        display:flex;
        flex-direction:column;
        border-radius:14px;
        border:1px solid rgba(94,200,179,.18);
        background:var(--surface);
        overflow:hidden;
    }
    .form-sticky {
        flex:0 0 auto;
        padding:1rem 1.15rem .65rem;
        background:var(--surface);
        border-bottom:1px solid rgba(94,200,179,.14);
        z-index:2;
    }
    .form-row { display:grid; gap:.55rem .85rem; align-items:end; margin-bottom:.65rem; }
    .form-row-meta { grid-template-columns:160px minmax(180px,1fr) minmax(180px,1fr); }
    .field label { display:block; font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:var(--gold-light); margin-bottom:.3rem; font-weight:600; }
    .field input,.field select { width:100%; padding:.55rem .65rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); font-family:inherit; font-size:.85rem; outline:none; }
    .field input:focus,.field select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(94,200,179,.12); }
    .field input[readonly] { opacity:.75; cursor:not-allowed; }
    .field select option { background:#2d0006; }
    .lines-head { display:flex; align-items:center; justify-content:space-between; margin:.15rem 0 0; }
    .lines-head h4 { color:var(--gold-light); font-size:.85rem; letter-spacing:.06em; text-transform:uppercase; margin:0; }

    .lines-scroll {
        flex:1 1 auto;
        min-height:0;
        overflow:auto;
        -webkit-overflow-scrolling:touch;
        padding:0 1.15rem;
    }
    .lines-table { width:100%; border-collapse:separate; border-spacing:0; min-width:780px; }
    .lines-table thead th {
        position:sticky;
        top:0;
        z-index:1;
        text-align:left;
        padding:.55rem .45rem;
        font-size:.68rem;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--gold-light);
        background:var(--surface);
        border-bottom:1px solid rgba(94,200,179,.22);
    }
    .lines-table tbody td { padding:.35rem .3rem; vertical-align:middle; }
    .lines-table .col-ref { width:110px; }
    .lines-table .col-des { width:auto; }
    .lines-table .col-num { width:95px; }
    .lines-table .col-act { width:42px; }
    .lines-table input { width:100%; padding:.45rem .5rem; border-radius:8px; border:1px solid rgba(94,200,179,.25); background:var(--bg-input); color:var(--text); font-size:.82rem; font-family:inherit; }
    .icon-btn { width:32px; height:32px; border-radius:8px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; }
    .icon-btn:hover { background:rgba(94,200,179,.18); }
    .icon-btn.danger:hover { color:#ff9a9a; border-color:rgba(255,100,100,.5); }
    .icon-btn svg { width:15px; height:15px; }

    .form-bottom {
        flex:0 0 auto;
        padding:.75rem 1.15rem 1rem;
        border-top:1px solid rgba(94,200,179,.18);
        background:var(--surface);
        z-index:2;
    }
    .totals-bar { display:flex; justify-content:flex-end; gap:1.5rem; margin-bottom:.75rem; color:var(--gold-light); font-weight:700; }
    .form-footer { display:flex; justify-content:flex-end; gap:.65rem; }
    @media (max-width:800px) {
        .form-row-meta { grid-template-columns:1fr 1fr; }
    }
    @media (max-width:560px) {
        .form-row-meta { grid-template-columns:1fr; }
        .fiche-page { height:auto; min-height:0; overflow:visible; }
        .form-shell { min-height:70vh; }
    }
</style>

<div class="content-panel fiche-page">
    <div class="alimenter-top">
        <div class="page-toolbar">
            <h2>Alimenter Dépôt</h2>
            <div class="toolbar-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif
        @if (session('success'))
            <div class="alert-ok">{{ session('success') }}</div>
        @endif
    </div>

    <form method="POST" action="{{ route('stock.alimenter_depot.store') }}" id="alimenterForm" class="form-shell">
        @csrf
        <datalist id="refCatalogue">
            @foreach ($references as $ref)
                <option value="{{ $ref['ref'] }}">{{ $ref['designation'] }}</option>
            @endforeach
        </datalist>
        <datalist id="desCatalogue">
            @foreach ($references as $ref)
                <option value="{{ $ref['designation'] }}">{{ $ref['ref'] }}</option>
            @endforeach
        </datalist>

        <div class="form-sticky">
            <div class="form-row form-row-meta">
                <div class="field">
                    <label for="field_date">Date</label>
                    <input type="date" name="date_mouvement" id="field_date" value="{{ old('date_mouvement', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="field">
                    <label for="field_depot">Dépôt</label>
                    <input type="text" id="field_depot" value="{{ $centralLabel }}" readonly>
                    <input type="hidden" name="depot" value="{{ $centralKey }}">
                </div>
                <div class="field">
                    <label for="field_destination">Destination</label>
                    <select name="depot_destination" id="field_destination" required>
                        <option value="">— Sélectionner —</option>
                        @foreach ($destinations as $key => $label)
                            <option value="{{ $key }}" @selected(old('depot_destination') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="lines-head">
                <h4>Articles</h4>
                <button type="button" class="btn btn-gold btn-sm" onclick="addLine()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Article
                </button>
            </div>
        </div>

        <div class="lines-scroll">
            <table class="lines-table">
                <thead>
                    <tr>
                        <th class="col-ref">Réf</th>
                        <th class="col-des">Désignation</th>
                        <th class="col-num">Qté</th>
                        <th class="col-num">Prix/U</th>
                        <th class="col-num">Sous-Total</th>
                        <th class="col-act"></th>
                    </tr>
                </thead>
                <tbody id="linesBody"></tbody>
            </table>
        </div>

        <div class="form-bottom">
            <div class="totals-bar">
                <span>Qté : <strong id="totalQte">0</strong></span>
                <span>Montant : <strong id="totalMontant">0,00</strong></span>
            </div>
            <div class="form-footer">
                <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
                <button type="submit" class="btn btn-gold">Valider</button>
            </div>
        </div>
    </form>
</div>

<script>
    const linesBody = document.getElementById('linesBody');
    const references = @json($references);
    let lineIndex = 0;

    function formatMoney(n) {
        return (Math.round(n * 100) / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function addLine(data = {}) {
        const i = lineIndex++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" class="js-ref" name="lignes[${i}][ref]" list="refCatalogue" value="${data.ref || ''}"></td>
            <td><input type="text" class="js-designation" name="lignes[${i}][designation]" list="desCatalogue" value="${data.designation || ''}" required></td>
            <td><input type="number" step="0.01" min="0.01" class="js-qte" name="lignes[${i}][qte]" value="${data.qte || 1}" required></td>
            <td><input type="number" step="0.01" min="0" class="js-pu" name="lignes[${i}][prix_unitaire]" value="${data.prix_unitaire || ''}" required></td>
            <td><input type="text" class="js-st" value="0,00" readonly></td>
            <td><button type="button" class="icon-btn danger" title="Retirer" onclick="removeLine(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button></td>
        `;
        linesBody.appendChild(tr);
        const refInput = tr.querySelector('.js-ref');
        const desInput = tr.querySelector('.js-designation');
        refInput.addEventListener('change', () => applyFromRef(tr));
        refInput.addEventListener('blur', () => applyFromRef(tr));
        desInput.addEventListener('change', () => applyFromDes(tr));
        desInput.addEventListener('blur', () => applyFromDes(tr));
        tr.querySelectorAll('.js-qte, .js-pu').forEach(el => el.addEventListener('input', () => recalcLine(tr)));
        recalcLine(tr);
    }

    function applyFromRef(tr) {
        const ref = (tr.querySelector('.js-ref')?.value || '').trim();
        if (!ref) return;
        const hit = references.find(r => r.ref.toLowerCase() === ref.toLowerCase());
        if (hit) tr.querySelector('.js-designation').value = hit.designation;
    }

    function applyFromDes(tr) {
        const designation = (tr.querySelector('.js-designation')?.value || '').trim();
        if (!designation) return;
        const hit = references.find(r => r.designation.toLowerCase() === designation.toLowerCase());
        if (hit && !tr.querySelector('.js-ref').value.trim()) {
            tr.querySelector('.js-ref').value = hit.ref;
        }
    }

    function removeLine(btn) {
        btn.closest('tr').remove();
        recalcTotals();
    }

    function recalcLine(tr) {
        const qte = parseFloat(tr.querySelector('.js-qte').value) || 0;
        const pu = parseFloat(tr.querySelector('.js-pu').value) || 0;
        tr.querySelector('.js-st').value = formatMoney(qte * pu);
        recalcTotals();
    }

    function recalcTotals() {
        let qte = 0, montant = 0;
        linesBody.querySelectorAll('tr').forEach(tr => {
            const q = parseFloat(tr.querySelector('.js-qte')?.value) || 0;
            const p = parseFloat(tr.querySelector('.js-pu')?.value) || 0;
            qte += q;
            montant += q * p;
        });
        document.getElementById('totalQte').textContent = formatMoney(qte);
        document.getElementById('totalMontant').textContent = formatMoney(montant);
    }

    document.getElementById('alimenterForm').addEventListener('submit', e => {
        const dest = document.getElementById('field_destination').value;
        if (!dest) {
            e.preventDefault();
            alert('Sélectionnez un dépôt destination.');
            return;
        }
        if (!linesBody.querySelector('tr')) {
            e.preventDefault();
            alert('Ajoutez au moins un article.');
        }
    });

    addLine();
</script>
@endsection
