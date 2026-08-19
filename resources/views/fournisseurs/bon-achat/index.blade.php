@extends('layouts.dashboard')

@section('title', "Bon D'achat")

@section('content')
<style>
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); letter-spacing:.04em; }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; transition:all .2s ease; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .btn-sm { padding:.45rem .75rem; font-size:.8rem; }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-size:.9rem; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:1050px; }
    .action-btns { display:flex; gap:.35rem; }
    .icon-btn { width:32px; height:32px; border-radius:8px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; }
    .icon-btn:hover { background:rgba(94,200,179,.18); }
    .icon-btn.danger:hover { color:#ff9a9a; border-color:rgba(255,100,100,.5); }
    .icon-btn svg { width:15px; height:15px; }
    .modal-backdrop { position:fixed; inset:0; background:rgba(7,11,20,.75); backdrop-filter:blur(6px); z-index:200; display:none; align-items:flex-start; justify-content:center; padding:1rem; overflow-y:auto; }
    .modal-backdrop.open { display:flex; }
    .modal-sheet { width:min(1100px,100%); margin:1rem auto 2rem; background:linear-gradient(160deg,rgba(84,0,11,.98),rgba(45,0,6,.99)); border:1px solid rgba(94,200,179,.35); border-radius:18px; box-shadow:0 20px 60px rgba(0,0,0,.55); padding:1.35rem 1.5rem; }
    .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid rgba(94,200,179,.2); }
    .modal-header h3 { font-family:'Fraunces', serif; color:var(--gold); font-size:1.2rem; }
    .form-grid { display:flex; flex-direction:column; gap:.75rem; margin-bottom:1rem; }
    .form-row { display:grid; gap:.65rem .85rem; align-items:end; }
    .form-row-date { grid-template-columns:160px; }
    .form-row-frns { grid-template-columns:140px 160px minmax(0,1fr); }
    .form-row-meta { grid-template-columns:repeat(4,minmax(0,1fr)); }
    .field label { display:block; font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:var(--gold-light); margin-bottom:.3rem; font-weight:600; }
    .field input,.field select { width:100%; padding:.55rem .65rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); font-family:inherit; font-size:.85rem; outline:none; }
    .field input:focus,.field select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(94,200,179,.12); }
    .field input:disabled,.field input[readonly],.field select:disabled { opacity:.75; cursor:not-allowed; }
    .field select option { background:#2d0006; }
    .lines-head { display:flex; align-items:center; justify-content:space-between; margin:1rem 0 .6rem; }
    .lines-head h4 { color:var(--gold-light); font-size:.85rem; letter-spacing:.06em; text-transform:uppercase; }
    .lines-table { width:100%; border-collapse:collapse; min-width:900px; }
    .lines-table .col-ref { width:90px; }
    .lines-table .col-des { width:auto; }
    .lines-table .col-sm { width:100px; }
    .lines-table .col-num { width:85px; }
    .lines-table .col-act { width:42px; }
    .lines-table input { width:100%; padding:.45rem .5rem; border-radius:8px; border:1px solid rgba(94,200,179,.25); background:var(--bg-input); color:var(--text); font-size:.82rem; font-family:inherit; }
    .totals-bar { display:flex; justify-content:flex-end; gap:1.5rem; margin-top:.85rem; padding-top:.75rem; border-top:1px solid rgba(94,200,179,.18); color:var(--gold-light); font-weight:700; }
    .modal-footer { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.1rem; padding-top:1rem; border-top:1px solid rgba(94,200,179,.18); }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    @media (max-width:900px) {
        .form-row-frns { grid-template-columns:1fr 1fr; }
        .form-row-frns .field-nom { grid-column:1 / -1; }
        .form-row-meta { grid-template-columns:1fr 1fr; }
    }
    @media (max-width:600px) {
        .form-row-date,.form-row-frns,.form-row-meta { grid-template-columns:1fr; }
        .form-row-frns .field-nom { grid-column:auto; }
    }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Bon D'achat</h2>
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

    @include('partials.kpi-grid', ['cards' => [
        ['label' => 'Total Achats', 'value' => $totalAchats, 'id' => 'kpiAchats', 'unit' => 'MAD'],
        ['label' => 'Total Paiement', 'value' => $totalPaiement, 'id' => 'kpiPaiement', 'unit' => 'MAD'],
        ['label' => 'Total Solde', 'value' => $totalSolde, 'id' => 'kpiSolde', 'unit' => 'MAD'],
    ]])

    <div class="filter-bar">
        <input type="search" data-filter="date" placeholder="Rechercher par date">
        <input type="search" data-filter="fournisseur" placeholder="Nom fournisseur">
        <input type="search" data-filter="numero" placeholder="N° Bon">
    </div>

    <div class="table-wrap">
        <table class="data-table" id="tableBons">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>ID Frns</th>
                    <th>Nom Fournisseur</th>
                    <th>Bon N°</th>
                    <th>Ville</th>
                    <th>Qte</th>
                    <th>Montant</th>
                    <th>Solde</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bons as $bon)
                    @php
                        $montant = (float) $bon->montant;
                        $solde = (float) $bon->solde;
                        $paiement = round($montant - $solde, 2);
                    @endphp
                    <tr data-row
                        data-date="{{ $bon->date_bon?->format('d/m/Y') }} {{ $bon->date_bon?->format('Y-m-d') }}"
                        data-fournisseur="{{ $bon->nom_fournisseur }}"
                        data-numero="{{ $bon->numero_bon }}"
                        data-sum="achats:{{ $montant }},paiement:{{ $paiement }},solde:{{ $solde }}">
                        <td>{{ $bon->date_bon?->format('d/m/Y') }}</td>
                        <td>{{ $bon->fournisseur?->ref_frns ?: $bon->fournisseur_id }}</td>
                        <td>{{ $bon->nom_fournisseur }}</td>
                        <td>{{ $bon->numero_bon }}</td>
                        <td>{{ $bon->ville ?: '—' }}</td>
                        <td>{{ number_format((float) $bon->qte_totale, 2, ',', ' ') }}</td>
                        <td>{{ number_format((float) $bon->montant, 2, ',', ' ') }}</td>
                        <td>{{ number_format((float) $bon->solde, 2, ',', ' ') }}</td>
                        <td>
                            <div class="action-btns">
                                <button type="button" class="icon-btn" title="Voir" onclick='openViewModal(@json($bon))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button type="button" class="icon-btn" title="Modifier" onclick='openEditModal(@json($bon))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('fournisseurs.bon_achat.destroy', $bon) }}" onsubmit="return confirm('Supprimer ce bon d\'achat ?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn danger" title="Supprimer">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>
                                <a href="{{ route('fournisseurs.bon_achat.print', $bon) }}" target="_blank" class="icon-btn" title="Imprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="9">Aucun bon d'achat enregistré.</td></tr>
                @endforelse
                    <tr class="empty-row js-filter-empty" style="display:none"><td colspan="9">Aucun résultat.</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop" id="baModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="modalTitle">Ajouter un bon d'achat</h3>
            <button type="button" class="icon-btn" onclick="closeModal()" title="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" id="baForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="form-grid">
                <div class="form-row form-row-date">
                    <div class="field">
                        <label for="field_date">Date</label>
                        <input type="date" name="date_bon" id="field_date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="form-row form-row-frns">
                    <div class="field">
                        <label for="field_numero">N° Bon</label>
                        <input type="text" id="field_numero" value="{{ $nextNumero }}" readonly>
                    </div>
                    <div class="field">
                        <label for="field_frns_id">ID Frns</label>
                        <select name="fournisseur_id" id="field_frns_id" required onchange="onFournisseurChange()">
                            <option value="">— Sélectionner —</option>
                            @foreach ($fournisseurs as $f)
                                <option
                                    value="{{ $f->id }}"
                                    data-nom="{{ $f->nom_fournisseur }}"
                                    data-ville="{{ $f->ville }}"
                                    data-type="{{ $f->type_reglement }}"
                                >{{ $f->id }} — {{ $f->ref_frns }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field field-nom">
                        <label for="field_frns_nom">Nom Fournisseur</label>
                        <input type="text" id="field_frns_nom" readonly>
                    </div>
                </div>
                <div class="form-row form-row-meta">
                    <div class="field">
                        <label for="field_ville">Ville</label>
                        <input type="text" id="field_ville" readonly>
                    </div>
                    <div class="field">
                        <label for="field_type">Type Régl</label>
                        <select name="type_reglement" id="field_type">
                            <option value="">— Sélectionner —</option>
                            @foreach ($typesReglement as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="field_echeance">Echéance</label>
                        <select name="echeance" id="field_echeance">
                            <option value="">— Sélectionner —</option>
                            @foreach ($echeances as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="field_depot">Dépôt</label>
                        <select name="depot" id="field_depot">
                            <option value="">— Sélectionner —</option>
                            @foreach ($depots as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="lines-head">
                <h4>Articles</h4>
                <button type="button" class="btn btn-gold btn-sm" id="btnAddLine" onclick="addLine()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Article
                </button>
            </div>

            <div class="table-wrap">
                <table class="lines-table">
                    <thead>
                        <tr>
                            <th class="col-ref">Réf</th>
                            <th class="col-des">Désignation</th>
                            <th class="col-sm">Famille</th>
                            <th class="col-sm">Catégorie</th>
                            <th class="col-num">Qte</th>
                            <th class="col-num">P/U</th>
                            <th class="col-num">Sous-Total</th>
                            <th class="col-act"></th>
                        </tr>
                    </thead>
                    <tbody id="linesBody"></tbody>
                </table>
            </div>

            <div class="totals-bar">
                <span>Qte : <strong id="totalQte">0</strong></span>
                <span>Montant : <strong id="totalMontant">0,00</strong></span>
            </div>

            <div class="modal-footer" id="modalFooter">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Fermer</button>
                <button type="submit" class="btn btn-gold" id="submitBtn">Valider</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('baModal');
    const form = document.getElementById('baForm');
    const linesBody = document.getElementById('linesBody');
    const storeUrl = @json(route('fournisseurs.bon_achat.store'));
    const updateBase = @json(url('/fournisseurs/bon-achat'));
    const nextNumero = @json($nextNumero);
    const today = @json(now()->format('Y-m-d'));
    let lineIndex = 0;
    let readonlyMode = false;

    function openModal() { modal.classList.add('open'); }
    function closeModal() {
        modal.classList.remove('open');
        setFormReadonly(false);
    }

    function onFournisseurChange() {
        const opt = document.getElementById('field_frns_id').selectedOptions[0];
        document.getElementById('field_frns_nom').value = opt?.dataset?.nom || '';
        document.getElementById('field_ville').value = opt?.dataset?.ville || '';
        if (opt?.dataset?.type) {
            document.getElementById('field_type').value = opt.dataset.type;
        }
    }

    function addLine(data = {}) {
        const i = lineIndex++;
        const tr = document.createElement('tr');
        tr.dataset.index = i;
        tr.innerHTML = `
            <td><input type="text" name="lignes[${i}][ref]" value="${data.ref || ''}" ${readonlyMode ? 'disabled' : ''}></td>
            <td><input type="text" name="lignes[${i}][designation]" value="${data.designation || ''}" required ${readonlyMode ? 'disabled' : ''}></td>
            <td><input type="text" name="lignes[${i}][famille]" value="${data.famille || ''}" ${readonlyMode ? 'disabled' : ''}></td>
            <td><input type="text" name="lignes[${i}][categorie]" value="${data.categorie || ''}" ${readonlyMode ? 'disabled' : ''}></td>
            <td><input type="number" step="0.01" min="0.01" class="js-qte" name="lignes[${i}][qte]" value="${data.qte || 1}" required ${readonlyMode ? 'disabled' : ''}></td>
            <td><input type="number" step="0.01" min="0" class="js-pu" name="lignes[${i}][prix_unitaire]" value="${data.prix_unitaire || 0}" required ${readonlyMode ? 'disabled' : ''}></td>
            <td><input type="text" class="js-st" value="${formatMoney((data.qte || 1) * (data.prix_unitaire || 0))}" readonly></td>
            <td>${readonlyMode ? '' : `<button type="button" class="icon-btn danger" title="Retirer" onclick="removeLine(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>`}</td>
        `;
        linesBody.appendChild(tr);
        tr.querySelectorAll('.js-qte, .js-pu').forEach(el => el.addEventListener('input', () => recalcLine(tr)));
        recalcTotals();
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

    function formatMoney(n) {
        return (Math.round(n * 100) / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function clearLines() {
        linesBody.innerHTML = '';
        lineIndex = 0;
    }

    function setFormReadonly(readonly) {
        readonlyMode = readonly;
        form.querySelectorAll('input, select, button').forEach(el => {
            if (el.id === 'field_numero' || el.id === 'field_frns_nom' || el.id === 'field_ville') return;
            if (el.classList.contains('js-st')) return;
            if (el.type === 'hidden') return;
            if (el.closest('.modal-footer') && el.id !== 'submitBtn') return;
            if (el.getAttribute('onclick') === 'closeModal()') return;
            if (el.id === 'btnAddLine') {
                el.style.display = readonly ? 'none' : 'inline-flex';
                return;
            }
            if (el.id === 'submitBtn') {
                el.style.display = readonly ? 'none' : 'inline-flex';
                return;
            }
            if (el.tagName === 'BUTTON' && el.classList.contains('icon-btn')) return;
            el.disabled = readonly;
        });
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = "Ajouter un bon d'achat";
        form.action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_date').value = today;
        document.getElementById('field_numero').value = nextNumero;
        document.getElementById('field_frns_id').value = '';
        document.getElementById('field_frns_nom').value = '';
        document.getElementById('field_ville').value = '';
        document.getElementById('field_type').value = '';
        document.getElementById('field_echeance').value = '';
        document.getElementById('field_depot').value = '';
        clearLines();
        setFormReadonly(false);
        addLine();
        openModal();
    }

    function fillForm(bon) {
        document.getElementById('field_date').value = (bon.date_bon || '').substring(0, 10);
        document.getElementById('field_numero').value = bon.numero_bon || '';
        document.getElementById('field_frns_id').value = bon.fournisseur_id || '';
        onFournisseurChange();
        document.getElementById('field_type').value = bon.type_reglement || '';
        document.getElementById('field_echeance').value = bon.echeance || '';
        document.getElementById('field_depot').value = bon.depot || '';
        clearLines();
        (bon.lignes || []).forEach(l => addLine({
            ref: l.ref,
            designation: l.designation,
            famille: l.famille,
            categorie: l.categorie,
            qte: l.qte,
            prix_unitaire: l.prix_unitaire,
        }));
        if (!(bon.lignes || []).length) addLine();
    }

    function openEditModal(bon) {
        document.getElementById('modalTitle').textContent = "Modifier bon d'achat";
        form.action = updateBase + '/' + bon.id;
        document.getElementById('formMethod').value = 'PUT';
        setFormReadonly(false);
        fillForm(bon);
        openModal();
    }

    function openViewModal(bon) {
        document.getElementById('modalTitle').textContent = "Détail bon d'achat";
        form.action = '#';
        setFormReadonly(true);
        fillForm(bon);
        openModal();
    }

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    damioBindTableFilters('tableBons', {
        onChange: function (_visible, sums) {
            document.getElementById('kpiAchats').innerHTML = damioFormatMoney(sums.achats || 0) + ' <span>MAD</span>';
            document.getElementById('kpiPaiement').innerHTML = damioFormatMoney(sums.paiement || 0) + ' <span>MAD</span>';
            document.getElementById('kpiSolde').innerHTML = damioFormatMoney(sums.solde || 0) + ' <span>MAD</span>';
        }
    });

    @if ($errors->any())
        openCreateModal();
    @endif
</script>
@endsection
