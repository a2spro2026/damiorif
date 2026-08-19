@extends('layouts.dashboard')

@section('title', 'Réglement Achat')

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
    .form-row-top { grid-template-columns:150px 140px minmax(0,1.4fr) 140px; }
    .field label { display:block; font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:var(--gold-light); margin-bottom:.3rem; font-weight:600; }
    .field input,.field select { width:100%; padding:.55rem .65rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); font-family:inherit; font-size:.85rem; outline:none; }
    .field input:focus,.field select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(94,200,179,.12); }
    .field input:disabled,.field input[readonly],.field select:disabled { opacity:.75; cursor:not-allowed; }
    .field select option { background:#2d0006; }
    .field-add { display:flex; align-items:flex-end; padding-bottom:2px; }
    .lines-head { display:flex; align-items:center; justify-content:space-between; margin:1rem 0 .6rem; }
    .lines-head h4 { color:var(--gold-light); font-size:.85rem; letter-spacing:.06em; text-transform:uppercase; }
    .bons-table { width:100%; border-collapse:collapse; min-width:780px; }
    .bons-table input { width:100%; padding:.45rem .5rem; border-radius:8px; border:1px solid rgba(94,200,179,.25); background:var(--bg-input); color:var(--text); font-size:.82rem; font-family:inherit; text-align:center; }
    .selected-table { width:100%; border-collapse:collapse; min-width:700px; margin-top:.5rem; }
    .totals-bar { display:flex; justify-content:flex-end; gap:1.5rem; margin-top:.85rem; padding-top:.75rem; border-top:1px solid rgba(94,200,179,.18); color:var(--gold-light); font-weight:700; }
    .modal-footer { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.1rem; padding-top:1rem; border-top:1px solid rgba(94,200,179,.18); }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .bons-hint { color:var(--text-muted); font-size:.82rem; margin:.35rem 0 .75rem; }
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .statut-badge {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:92px; padding:.28rem .65rem; border-radius:999px;
        font-size:.72rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase;
        color:#fff; border:1px solid rgba(255,255,255,.15);
    }
    .statut-form { display:inline-flex; justify-content:center; margin:0; }
    .statut-wrap {
        position:relative;
        display:inline-flex;
        align-items:center;
        width:100%;
        max-width:170px;
    }
    .statut-wrap::after {
        content:"";
        position:absolute;
        right:0.75rem;
        top:50%;
        width:0.55rem;
        height:0.55rem;
        border-right:2.5px solid currentColor;
        border-bottom:2.5px solid currentColor;
        transform:translateY(-65%) rotate(45deg);
        pointer-events:none;
        z-index:2;
        opacity:0.95;
        filter: drop-shadow(0 1px 1px rgba(0,0,0,.35));
    }
    .statut-wrap.statut-tone-reporte::after {
        border-color:#1a1200;
    }
    .statut-wrap.statut-tone-en_instance::after,
    .statut-wrap.statut-tone-en_cours::after,
    .statut-wrap.statut-tone-paye::after,
    .statut-wrap.statut-tone-imp::after,
    .statut-wrap.statut-tone-devalide::after {
        border-color:#fff;
    }
    .statut-select {
        appearance:none; -webkit-appearance:none; -moz-appearance:none;
        position:relative;
        width:100%;
        min-width:132px;
        padding:.55rem 2.1rem .55rem .9rem;
        border-radius:999px;
        border:1px solid rgba(255,255,255,.28);
        font-family:inherit;
        font-size:.7rem;
        font-weight:800;
        letter-spacing:.08em;
        text-transform:uppercase;
        color:var(--text);
        cursor:pointer;
        text-align:center;
        outline:none;
        background-repeat:no-repeat;
        background-position:center;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.35),
            inset 0 -1px 0 rgba(0,0,0,.18),
            0 4px 14px rgba(0,0,0,.28);
        transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }
    .statut-select:hover {
        transform: translateY(-1px) scale(1.03);
        filter: brightness(1.08);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.45),
            0 8px 20px rgba(0,0,0,.35);
    }
    .statut-select:focus {
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.4),
            0 0 0 3px rgba(94,200,179,.28),
            0 8px 22px rgba(0,0,0,.35);
    }
    .statut-select option {
        color:#1a1a1a;
        background:#fff;
        text-transform:none;
        font-weight:600;
        letter-spacing:0;
        font-size:.85rem;
    }
    .statut-en_instance {
        background: linear-gradient(180deg, #9ca3af 0%, #6b7280 48%, #4b5563 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.35), 0 4px 14px rgba(107,114,128,.45);
    }
    .statut-en_cours {
        background: linear-gradient(180deg, #60a5fa 0%, #3b82f6 45%, #1d4ed8 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.35), 0 4px 16px rgba(37,99,235,.5);
    }
    .statut-paye {
        background: linear-gradient(180deg, #4ade80 0%, #22c55e 45%, #15803d 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.35), 0 4px 16px rgba(22,163,74,.5);
    }
    .statut-imp {
        background: linear-gradient(180deg, #f87171 0%, #ef4444 45%, #b91c1c 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.35), 0 4px 16px rgba(220,38,38,.5);
    }
    .statut-reporte {
        background: linear-gradient(180deg, #fde047 0%, #eab308 48%, #a16207 100%);
        color:#1a1200;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.45), 0 4px 16px rgba(202,138,4,.45);
    }
    .statut-devalide {
        background: linear-gradient(180deg, #c084fc 0%, #a855f7 45%, #7e22ce 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.35), 0 4px 16px rgba(147,51,234,.5);
    }
    #field_statut.statut-select,
    .field #field_statut.statut-select {
        width:100%;
        min-width:0;
        border-radius:12px;
        text-align:left;
        padding:.62rem 2.2rem .62rem .85rem;
        font-size:.78rem;
        border-color:rgba(255,255,255,.25);
    }
    .field .statut-wrap { max-width:none; }
    .form-row-pay { grid-template-columns:minmax(0,1fr) minmax(0,1.1fr) 130px 140px 150px auto; }
    @media (max-width:900px) {
        .form-row-top,.form-row-pay { grid-template-columns:1fr 1fr; }
        .field-add { grid-column:1 / -1; }
    }
    @media (max-width:600px) {
        .form-row-top,.form-row-pay { grid-template-columns:1fr; }
    }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Réglement Achat</h2>
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
        ['label' => 'Total Chq', 'value' => $totauxType['cheque'], 'id' => 'kpiChq', 'unit' => 'MAD'],
        ['label' => 'Total Eff', 'value' => $totauxType['effet'], 'id' => 'kpiEff', 'unit' => 'MAD'],
        ['label' => 'Total Esp', 'value' => $totauxType['especes'], 'id' => 'kpiEsp', 'unit' => 'MAD'],
        ['label' => 'Total Vir', 'value' => $totauxType['virement'], 'id' => 'kpiVir', 'unit' => 'MAD'],
        ['label' => 'Total Vers', 'value' => $totauxType['versement'], 'id' => 'kpiVers', 'unit' => 'MAD'],
    ]])

    <div class="filter-bar">
        <input type="search" data-filter="numero" placeholder="N° Régl">
        <input type="search" data-filter="montant" placeholder="Montant">
    </div>

    <div class="table-wrap">
        <table class="data-table" id="tableReglements">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N°</th>
                    <th>Fournisseur</th>
                    <th>Type</th>
                    <th>Bnq</th>
                    <th>Tiré</th>
                    <th>Montant Régl</th>
                    <th>Décaiss</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reglements as $r)
                    @php
                        $family = \App\Support\TypesReglement::familyKey($r->type_reglement);
                        $montant = (float) $r->montant;
                        $sumAttr = $family ? $family.':'.$montant : '';
                    @endphp
                    <tr data-row
                        data-numero="{{ $r->numero }}"
                        data-montant="{{ number_format($montant, 2, ',', ' ') }} {{ number_format($montant, 2, '.', '') }}"
                        data-sum="{{ $sumAttr }}">
                        <td>{{ $r->date_reglement?->format('d/m/Y') }}</td>
                        <td>{{ $r->numero }}</td>
                        <td>{{ $r->nom_fournisseur }}</td>
                        <td>{{ $typesReglement[$r->type_reglement] ?? ($r->type_reglement ?: '—') }}</td>
                        <td>{{ $r->banque ?: '—' }}</td>
                        <td>{{ $r->nom_tire ?: '—' }}</td>
                        <td>{{ number_format((float) $r->montant, 2, ',', ' ') }}</td>
                        <td>{{ $r->date_decaissement?->format('d/m/Y') ?: '—' }}</td>
                        <td>
                            @php $statutKey = $r->statut ?: 'en_instance'; @endphp
                            <form method="POST" action="{{ route('fournisseurs.reglement_achat.statut', $r) }}" class="statut-form">
                                @csrf
                                @method('PATCH')
                                <div class="statut-wrap statut-tone-{{ $statutKey }}">
                                    <select name="statut" class="statut-select statut-{{ $statutKey }}" onchange="this.form.submit()">
                                        @foreach ($statuts as $value => $label)
                                            <option value="{{ $value }}" @selected($value === $statutKey)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button type="button" class="icon-btn" title="Voir" onclick='openViewModal(@json($r))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button type="button" class="icon-btn" title="Modifier" onclick='openEditModal(@json($r))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('fournisseurs.reglement_achat.destroy', $r) }}" onsubmit="return confirm('Supprimer ce réglement ?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn danger" title="Supprimer">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>
                                <a href="{{ route('fournisseurs.reglement_achat.print', $r) }}" target="_blank" class="icon-btn" title="Imprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="10">Aucun réglement d'achat enregistré.</td></tr>
                @endforelse
                    <tr class="empty-row js-filter-empty" style="display:none"><td colspan="10">Aucun résultat.</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop" id="raModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="modalTitle">Ajouter un réglement d'achat</h3>
            <button type="button" class="icon-btn" onclick="closeModal()" title="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" id="raForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="form-grid">
                <div class="form-row form-row-top">
                    <div class="field">
                        <label for="field_date">Date</label>
                        <input type="date" name="date_reglement" id="field_date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="field">
                        <label for="field_numero">N°</label>
                        <input type="text" id="field_numero" value="{{ $nextNumero }}" readonly>
                    </div>
                    <div class="field">
                        <label for="field_frns">Nom Fournisseur</label>
                        <select name="fournisseur_id" id="field_frns" required onchange="onFournisseurChange()">
                            <option value="">— Sélectionner —</option>
                            @foreach ($fournisseurs as $f)
                                <option
                                    value="{{ $f->id }}"
                                    data-type="{{ $f->type_reglement }}"
                                    data-banque="{{ $f->banque }}"
                                    data-nom="{{ $f->nom_fournisseur }}"
                                >{{ $f->nom_fournisseur }} ({{ $f->ref_frns }})</option>
                            @endforeach
                        </select>
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
                </div>

                <div class="form-row form-row-pay">
                    <div class="field">
                        <label for="field_banque">Bnq</label>
                        <input type="text" name="banque" id="field_banque" list="banquesList">
                        <datalist id="banquesList">
                            @foreach ($banques as $banque)
                                <option value="{{ $banque }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="field">
                        <label for="field_tire">Nom Tiré</label>
                        <input type="text" name="nom_tire" id="field_tire">
                    </div>
                    <div class="field">
                        <label for="field_montant">Montant Régl</label>
                        <input type="number" step="0.01" min="0" name="montant" id="field_montant" value="0">
                    </div>
                    <div class="field">
                        <label for="field_decaiss">Date Décaiss</label>
                        <input type="date" name="date_decaissement" id="field_decaiss">
                    </div>
                    <div class="field">
                        <label for="field_statut">Statut</label>
                        <div class="statut-wrap statut-tone-en_instance" id="field_statut_wrap">
                            <select name="statut" id="field_statut" class="statut-select statut-en_instance" required onchange="syncStatutSelectColor(this)">
                                @foreach ($statuts as $value => $label)
                                    <option value="{{ $value }}" @selected($value === 'en_instance')>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="field field-add">
                        <button type="button" class="icon-btn" id="btnAddBon" title="Appliquer le montant" onclick="addSelectedBons()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="lines-head">
                <h4>Bons non soldés</h4>
            </div>
            <p class="bons-hint" id="bonsHint">Sélectionnez un fournisseur pour afficher ses bons non soldés.</p>
            <div class="table-wrap" id="bonsWrap" style="display:none;">
                <table class="bons-table data-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Bon N°</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Solde</th>
                            <th>Montant Régl</th>
                        </tr>
                    </thead>
                    <tbody id="bonsBody"></tbody>
                </table>
            </div>

            <div class="totals-bar">
                <span>Total réglement : <strong id="totalMontant">0,00</strong></span>
            </div>

            <div class="modal-footer" id="modalFooter">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Fermer</button>
                <button type="submit" class="btn btn-gold" id="submitBtn">Valider</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('raModal');
    const form = document.getElementById('raForm');
    const storeUrl = @json(route('fournisseurs.reglement_achat.store'));
    const updateBase = @json(url('/fournisseurs/reglement-achat'));
    const nextNumero = @json($nextNumero);
    const today = @json(now()->format('Y-m-d'));
    const bonsNonSoldes = @json($bonsNonSoldes);
    let selectedLignes = [];
    let readonlyMode = false;
    let editBonusByBonId = {};


    function syncStatutSelectColor(el) {
        const key = el.value || 'en_instance';
        el.className = 'statut-select statut-' + key;
        const wrap = el.closest('.statut-wrap') || document.getElementById('field_statut_wrap');
        if (wrap) {
            wrap.className = 'statut-wrap statut-tone-' + key;
        }
    }
    function openModal() { modal.classList.add('open'); }
    function closeModal() {
        modal.classList.remove('open');
        setFormReadonly(false);
        editBonusByBonId = {};
    }

    function formatMoney(n) {
        return (Math.round(n * 100) / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function onFournisseurChange() {
        const opt = document.getElementById('field_frns').selectedOptions[0];
        if (opt?.dataset?.type) document.getElementById('field_type').value = opt.dataset.type;
        if (opt?.dataset?.banque) document.getElementById('field_banque').value = opt.dataset.banque;
        if (opt?.dataset?.nom && !document.getElementById('field_tire').value) {
            document.getElementById('field_tire').value = opt.dataset.nom;
        }
        renderUnpaidBons();
    }

    function availableSolde(bon) {
        return (parseFloat(bon.solde) || 0) + (editBonusByBonId[bon.id] || 0);
    }

    function renderUnpaidBons() {
        const frnsId = document.getElementById('field_frns').value;
        const body = document.getElementById('bonsBody');
        const wrap = document.getElementById('bonsWrap');
        const hint = document.getElementById('bonsHint');
        body.innerHTML = '';

        if (!frnsId) {
            wrap.style.display = 'none';
            hint.style.display = 'block';
            hint.textContent = 'Sélectionnez un fournisseur pour afficher ses bons non soldés.';
            return;
        }

        const bons = bonsNonSoldes
            .filter(b => String(b.fournisseur_id) === String(frnsId))
            .map(b => ({ ...b, solde_dispo: availableSolde(b) }))
            .filter(b => b.solde_dispo > 0.009 || selectedLignes.some(l => String(l.bon_achat_id) === String(b.id)));

        // Include bons already selected that may be missing from unpaid list
        selectedLignes.forEach(l => {
            if (!bons.some(b => String(b.id) === String(l.bon_achat_id))) {
                const found = bonsNonSoldes.find(b => String(b.id) === String(l.bon_achat_id));
                if (found) {
                    bons.push({ ...found, solde_dispo: availableSolde(found) });
                } else {
                    bons.push({
                        id: l.bon_achat_id,
                        numero_bon: l.numero_bon,
                        date_bon: null,
                        montant: l.montant_applique,
                        solde: 0,
                        solde_dispo: (editBonusByBonId[l.bon_achat_id] || 0),
                        fournisseur_id: frnsId,
                    });
                }
            }
        });

        if (!bons.length) {
            wrap.style.display = 'none';
            hint.style.display = 'block';
            hint.textContent = 'Aucun bon non soldé pour ce fournisseur.';
            return;
        }

        hint.style.display = 'none';
        wrap.style.display = 'block';

        bons.forEach(bon => {
            const already = selectedLignes.find(l => String(l.bon_achat_id) === String(bon.id));
            const montantBon = parseFloat(bon.montant) || 0;
            const soldeDispo = bon.solde_dispo;
            const montantRegleDefaut = already ? already.montant_applique : 0;
            const soldeAffiche = Math.max(0, Math.round((soldeDispo - montantRegleDefaut) * 100) / 100);
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" class="js-check" data-id="${bon.id}" ${already ? 'checked' : ''} ${readonlyMode ? 'disabled' : ''}></td>
                <td>${bon.numero_bon || ''}</td>
                <td>${bon.date_bon ? String(bon.date_bon).substring(0, 10).split('-').reverse().join('/') : '—'}</td>
                <td class="js-montant-bon">${formatMoney(montantBon)}</td>
                <td class="js-solde">${formatMoney(soldeAffiche)}</td>
                <td><input type="number" step="0.01" min="0" max="${soldeDispo}" class="js-amount"
                    data-id="${bon.id}"
                    data-montant="${montantBon}"
                    data-solde="${soldeDispo}"
                    data-numero="${bon.numero_bon || ''}"
                    value="${montantRegleDefaut || ''}"
                    ${readonlyMode ? 'disabled' : ''}></td>
            `;
            body.appendChild(tr);
            const amountInput = tr.querySelector('.js-amount');
            amountInput.addEventListener('input', () => recalcRowSolde(tr));
        });
    }

    function recalcRowSolde(tr) {
        const amountInput = tr.querySelector('.js-amount');
        const soldeCell = tr.querySelector('.js-solde');
        const soldeDispo = parseFloat(amountInput.dataset.solde) || 0;
        let montantRegle = parseFloat(amountInput.value) || 0;
        if (montantRegle > soldeDispo) {
            montantRegle = soldeDispo;
            amountInput.value = soldeDispo.toFixed(2);
        }
        if (montantRegle < 0) {
            montantRegle = 0;
            amountInput.value = '';
        }
        const nouveauSolde = Math.max(0, Math.round((soldeDispo - montantRegle) * 100) / 100);
        soldeCell.textContent = formatMoney(nouveauSolde);
        if (montantRegle > 0) {
            tr.querySelector('.js-check').checked = true;
        }
        syncMontantFromBons();
    }

    function collectLignesFromBons() {
        const lignes = [];
        document.querySelectorAll('#bonsBody tr').forEach(tr => {
            const check = tr.querySelector('.js-check');
            const amountInput = tr.querySelector('.js-amount');
            if (!check || !amountInput) return;
            let montant = parseFloat(amountInput.value) || 0;
            const solde = parseFloat(amountInput.dataset.solde) || 0;
            if (montant > solde) montant = solde;
            if ((!check.checked && montant <= 0) || montant <= 0) return;
            lignes.push({
                bon_achat_id: amountInput.dataset.id,
                numero_bon: amountInput.dataset.numero,
                montant_applique: montant,
            });
        });
        return lignes;
    }

    function syncHiddenLignes() {
        form.querySelectorAll('input.js-ligne-hidden').forEach(el => el.remove());
        const lignes = collectLignesFromBons();
        lignes.forEach((l, i) => {
            const h1 = document.createElement('input');
            h1.type = 'hidden';
            h1.name = `lignes[${i}][bon_achat_id]`;
            h1.value = l.bon_achat_id;
            h1.className = 'js-ligne-hidden';
            form.appendChild(h1);

            const h2 = document.createElement('input');
            h2.type = 'hidden';
            h2.name = `lignes[${i}][montant_applique]`;
            h2.value = l.montant_applique;
            h2.className = 'js-ligne-hidden';
            form.appendChild(h2);
        });
        return lignes;
    }

    function addSelectedBons() {
        if (readonlyMode) return;
        const montantSaisi = parseFloat(document.getElementById('field_montant').value) || 0;
        const rows = [...document.querySelectorAll('#bonsBody tr')];
        const targets = rows.filter(tr => tr.querySelector('.js-check')?.checked);
        const list = targets.length ? targets : rows;

        if (!list.length) {
            alert('Sélectionnez un fournisseur avec des bons non soldés.');
            return;
        }

        if (montantSaisi > 0 && list.length === 1) {
            const tr = list[0];
            const amountInput = tr.querySelector('.js-amount');
            const soldeDispo = parseFloat(amountInput.dataset.solde) || 0;
            amountInput.value = Math.min(montantSaisi, soldeDispo).toFixed(2);
            tr.querySelector('.js-check').checked = true;
            recalcRowSolde(tr);
            return;
        }

        let remaining = montantSaisi;
        list.forEach(tr => {
            const amountInput = tr.querySelector('.js-amount');
            const soldeDispo = parseFloat(amountInput.dataset.solde) || 0;
            if (montantSaisi > 0) {
                const applique = Math.min(remaining, soldeDispo);
                amountInput.value = applique > 0 ? applique.toFixed(2) : '';
                remaining = Math.round((remaining - applique) * 100) / 100;
                tr.querySelector('.js-check').checked = applique > 0;
            } else if (tr.querySelector('.js-check').checked && !amountInput.value) {
                amountInput.value = soldeDispo.toFixed(2);
            }
            recalcRowSolde(tr);
        });
    }

    function syncMontantFromBons() {
        const total = collectLignesFromBons().reduce((s, l) => s + l.montant_applique, 0);
        const field = document.getElementById('field_montant');
        if (!field.dataset.manual) {
            field.value = total ? total.toFixed(2) : '';
        }
        document.getElementById('totalMontant').textContent = formatMoney(parseFloat(field.value) || total);
    }

    document.getElementById('field_montant').addEventListener('input', function () {
        this.dataset.manual = '1';
        const montantSaisi = parseFloat(this.value) || 0;
        document.getElementById('totalMontant').textContent = formatMoney(montantSaisi);

        const rows = [...document.querySelectorAll('#bonsBody tr')];
        const checked = rows.filter(tr => tr.querySelector('.js-check')?.checked);
        const targetRows = checked.length ? checked : (rows.length === 1 ? rows : []);

        if (targetRows.length === 1) {
            const tr = targetRows[0];
            const amountInput = tr.querySelector('.js-amount');
            const soldeDispo = parseFloat(amountInput.dataset.solde) || 0;
            const applique = Math.min(montantSaisi, soldeDispo);
            amountInput.value = applique ? applique.toFixed(2) : '';
            tr.querySelector('.js-check').checked = applique > 0;
            const soldeCell = tr.querySelector('.js-solde');
            soldeCell.textContent = formatMoney(Math.max(0, Math.round((soldeDispo - applique) * 100) / 100));
        }
    });

    function setFormReadonly(readonly) {
        readonlyMode = readonly;
        form.querySelectorAll('input, select, button').forEach(el => {
            if (el.type === 'hidden') return;
            if (el.id === 'field_numero') return;
            if (el.closest('.modal-footer') && el.id !== 'submitBtn') return;
            if (el.getAttribute('onclick') === 'closeModal()') return;
            if (el.id === 'btnAddBon') {
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

    function resetForm() {
        document.getElementById('field_date').value = today;
        document.getElementById('field_numero').value = nextNumero;
        document.getElementById('field_frns').value = '';
        document.getElementById('field_type').value = '';
        document.getElementById('field_banque').value = '';
        document.getElementById('field_tire').value = '';
        document.getElementById('field_decaiss').value = '';
        document.getElementById('field_statut').value = 'en_instance';
        syncStatutSelectColor(document.getElementById('field_statut'));
        const montantField = document.getElementById('field_montant');
        montantField.value = '';
        delete montantField.dataset.manual;
        selectedLignes = [];
        editBonusByBonId = {};
        form.querySelectorAll('input.js-ligne-hidden').forEach(el => el.remove());
        renderUnpaidBons();
        syncMontantFromBons();
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = "Ajouter un réglement d'achat";
        form.action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        setFormReadonly(false);
        resetForm();
        openModal();
    }

    function fillForm(r) {
        document.getElementById('field_date').value = (r.date_reglement || '').substring(0, 10);
        document.getElementById('field_numero').value = r.numero || '';
        document.getElementById('field_frns').value = r.fournisseur_id || '';
        document.getElementById('field_type').value = r.type_reglement || '';
        document.getElementById('field_banque').value = r.banque || '';
        document.getElementById('field_tire').value = r.nom_tire || '';
        document.getElementById('field_decaiss').value = (r.date_decaissement || '').substring(0, 10);
        document.getElementById('field_statut').value = r.statut || 'en_instance';
        syncStatutSelectColor(document.getElementById('field_statut'));
        const montantField = document.getElementById('field_montant');
        montantField.value = r.montant != null ? parseFloat(r.montant).toFixed(2) : '';
        montantField.dataset.manual = '1';

        editBonusByBonId = {};
        selectedLignes = (r.lignes || []).map(l => {
            editBonusByBonId[l.bon_achat_id] = parseFloat(l.montant_applique) || 0;
            return {
                bon_achat_id: l.bon_achat_id,
                numero_bon: l.numero_bon,
                montant_applique: parseFloat(l.montant_applique) || 0,
            };
        });

        renderUnpaidBons();
        syncMontantFromBons();
    }

    function openEditModal(r) {
        document.getElementById('modalTitle').textContent = 'Modifier réglement d\'achat';
        form.action = updateBase + '/' + r.id;
        document.getElementById('formMethod').value = 'PUT';
        setFormReadonly(false);
        fillForm(r);
        openModal();
    }

    function openViewModal(r) {
        document.getElementById('modalTitle').textContent = 'Détail réglement d\'achat';
        form.action = '#';
        setFormReadonly(true);
        fillForm(r);
        openModal();
    }

    form.addEventListener('submit', function (e) {
        if (readonlyMode) {
            e.preventDefault();
            return;
        }
        const lignes = syncHiddenLignes();
        if (!lignes.length) {
            e.preventDefault();
            alert('Saisissez un montant réglé sur au moins un bon non soldé.');
        }
    });

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    damioBindTableFilters('tableReglements', {
        onChange: function (_visible, sums) {
            document.getElementById('kpiChq').innerHTML = damioFormatMoney(sums.cheque || 0) + ' <span>MAD</span>';
            document.getElementById('kpiEff').innerHTML = damioFormatMoney(sums.effet || 0) + ' <span>MAD</span>';
            document.getElementById('kpiEsp').innerHTML = damioFormatMoney(sums.especes || 0) + ' <span>MAD</span>';
            document.getElementById('kpiVir').innerHTML = damioFormatMoney(sums.virement || 0) + ' <span>MAD</span>';
            document.getElementById('kpiVers').innerHTML = damioFormatMoney(sums.versement || 0) + ' <span>MAD</span>';
        }
    });

    @if ($errors->any())
        openCreateModal();
    @endif
</script>
@endsection
