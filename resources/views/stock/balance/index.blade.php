@extends('layouts.dashboard')

@section('title', 'Balance Dépôt')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:.75rem; margin-bottom:.85rem; flex-wrap:nowrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); white-space:nowrap; }
    .page-meta { font-size:.78rem; color:var(--text-muted); margin-top:.2rem; }
    .toolbar-actions { display:flex; gap:.45rem; flex-wrap:nowrap; align-items:center; margin-left:auto; }
    .btn { display:inline-flex; align-items:center; gap:.35rem; padding:.4rem .75rem; border-radius:8px; font-family:inherit; font-size:.78rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; white-space:nowrap; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .filter-bar { display:flex; gap:.4rem; flex-wrap:nowrap; align-items:center; flex-shrink:0; }
    .filter-bar select,
    .filter-bar input {
        padding:.35rem .55rem; border-radius:8px; border:1px solid rgba(94,200,179,.3);
        background:var(--bg-input); color:var(--text); font-family:inherit; font-size:.78rem; outline:none; height:32px; box-sizing:border-box;
    }
    .filter-bar input[type="month"] { width:9.2rem; }
    .filter-bar select { width:10.5rem; max-width:10.5rem; }
    .filter-bar input[type="search"] { width:7.5rem; min-width:0; }
    .filter-bar input:focus,.filter-bar select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(94,200,179,.12); }
    .filter-bar select option { background:#2d0006; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:780px; }
    .data-table tbody tr { cursor:pointer; }
    .data-table tbody tr:hover td { background:rgba(94,200,179,.1) !important; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; cursor:default; }
    .action-btns { display:flex; justify-content:center; gap:.35rem; }
    .icon-btn {
        width:32px; height:32px; border-radius:8px; border:1px solid rgba(94,200,179,.3);
        background:var(--bg-input); color:var(--gold); display:inline-flex; align-items:center; justify-content:center;
        cursor:pointer; text-decoration:none;
    }
    .icon-btn:hover { background:rgba(94,200,179,.18); }
    .icon-btn svg { width:15px; height:15px; }
    .totals-bar { display:flex; justify-content:flex-end; gap:1.5rem; margin-top:.85rem; padding-top:.75rem; border-top:1px solid rgba(94,200,179,.18); color:var(--gold-light); font-weight:700; flex-wrap:wrap; }
    .modal-backdrop {
        position:fixed; inset:0; background:rgba(7,11,20,.72); backdrop-filter:blur(6px);
        z-index:200; display:none; align-items:center; justify-content:center; padding:1rem;
    }
    .modal-backdrop.open { display:flex; }
    .modal-sheet {
        width:min(920px,100%); max-height:90vh; overflow:auto;
        background:linear-gradient(160deg,rgba(84,0,11,.97),rgba(45,0,6,.98));
        border:1px solid rgba(94,200,179,.35); border-radius:18px;
        box-shadow:0 20px 60px rgba(0,0,0,.55); padding:1.35rem 1.5rem;
    }
    .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid rgba(94,200,179,.2); }
    .modal-header h3 { font-family:'Fraunces', serif; color:var(--gold); font-size:1.2rem; }
    .modal-meta { display:flex; flex-wrap:wrap; gap:.75rem 1.25rem; color:var(--text-soft); font-size:.88rem; margin-bottom:1rem; }
    .lines-table { width:100%; border-collapse:collapse; min-width:640px; }
    .modal-footer { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.1rem; padding-top:1rem; border-top:1px solid rgba(94,200,179,.18); }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <div>
            <h2>Balance{{ $depotLabel ? ' — '.$depotLabel : '' }}</h2>
            <div class="page-meta">Bons d’alimentation DamioRif · {{ $rows->count() }} bon{{ $rows->count() > 1 ? 's' : '' }}</div>
        </div>
        <form method="GET" action="{{ route('stock.balance') }}" class="filter-bar" id="balanceFilters">
            <input type="month" name="mois" value="{{ $mois }}" onchange="this.form.submit()" title="Mois">
            @if ($isCentral)
                <select name="depot" onchange="this.form.submit()" title="Dépôt">
                    <option value="">Tous dépôts</option>
                    @foreach ($depotOptions as $key => $label)
                        <option value="{{ $key }}" @selected($selectedDepot === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="depot" value="{{ $selectedDepot }}">
            @endif
            <input type="search" name="numero" value="{{ $numero }}" placeholder="N° Bon" autocomplete="off">
            <button type="submit" class="btn btn-ghost">OK</button>
        </form>
        <div class="toolbar-actions">
            <a href="{{ route('stock.balance.print', request()->query()) }}" target="_blank" class="btn btn-gold">Imprimer</a>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="balanceTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° Bon Alimentation</th>
                    @if ($isCentral)
                        <th>Dépôt</th>
                    @endif
                    <th>Montant</th>
                    <th>Solde</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr data-bon-id="{{ $row['id'] }}" ondblclick="openBonPanel({{ $row['id'] }})">
                        <td>{{ $row['date'] }}</td>
                        <td><strong>{{ $row['numero'] }}</strong></td>
                        @if ($isCentral)
                            <td>{{ $row['depot_label'] }}</td>
                        @endif
                        <td>{{ number_format($row['montant'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($row['solde'], 2, ',', ' ') }}</td>
                        <td onclick="event.stopPropagation()">
                            <div class="action-btns">
                                <button type="button" class="icon-btn" title="Voir" onclick="openBonPanel({{ $row['id'] }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <a class="icon-btn" title="Imprimer" href="{{ route('stock.balance.bon.print', $row['id']) }}" target="_blank">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                                </a>
                                <a class="icon-btn" title="Télécharger PDF" href="{{ route('stock.balance.bon.print', ['mouvement' => $row['id'], 'pdf' => 1]) }}" target="_blank">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="M9 15l3 3 3-3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="{{ $isCentral ? 6 : 5 }}">Aucun bon d’alimentation.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="totals-bar">
        <span>Montant : {{ number_format($totalMontant, 2, ',', ' ') }} MAD</span>
        <span>Solde : {{ number_format($totalSolde, 2, ',', ' ') }} MAD</span>
    </div>
</div>

<div class="modal-backdrop" id="bonModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="bonModalTitle">Bon d’alimentation</h3>
            <button type="button" class="icon-btn" onclick="closeBonPanel()" title="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-meta" id="bonModalMeta"></div>
        <div class="table-wrap" style="border:none; background:transparent;">
            <table class="lines-table data-table">
                <thead>
                    <tr>
                        <th>Réf</th>
                        <th>Désignation</th>
                        <th>Qté</th>
                        <th>Prix/U</th>
                        <th>Sous-Total</th>
                    </tr>
                </thead>
                <tbody id="bonModalBody"></tbody>
            </table>
        </div>
        <div class="modal-footer">
            <a href="#" target="_blank" class="btn btn-gold" id="bonModalPrint">Imprimer</a>
            <button type="button" class="btn btn-ghost" onclick="closeBonPanel()">Fermer</button>
        </div>
    </div>
</div>

<script>
    const bonsById = @json($rows->keyBy('id'));
    const isCentral = @json($isCentral);
    const money = (n) => (Math.round((n || 0) * 100) / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function openBonPanel(id) {
        const bon = bonsById[id];
        if (!bon) return;
        document.getElementById('bonModalTitle').textContent = 'Bon ' + bon.numero;
        document.getElementById('bonModalMeta').innerHTML =
            `<span>Date : <strong>${bon.date || '—'}</strong></span>` +
            (isCentral ? `<span>Dépôt : <strong>${bon.depot_label || '—'}</strong></span>` : '') +
            `<span>Montant : <strong>${money(bon.montant)} MAD</strong></span>` +
            `<span>Solde : <strong>${money(bon.solde)} MAD</strong></span>`;
        const body = document.getElementById('bonModalBody');
        body.innerHTML = (bon.lignes || []).map(l =>
            `<tr>
                <td>${l.ref || '—'}</td>
                <td>${l.designation || ''}</td>
                <td>${money(l.qte)}</td>
                <td>${money(l.prix_unitaire)}</td>
                <td>${money(l.sous_total)}</td>
            </tr>`
        ).join('') || `<tr class="empty-row"><td colspan="5">Aucun article.</td></tr>`;
        document.getElementById('bonModalPrint').href = @json(url('/stock/balance')) + '/' + id + '/print';
        document.getElementById('bonModal').classList.add('open');
    }

    function closeBonPanel() {
        document.getElementById('bonModal').classList.remove('open');
    }

    document.getElementById('bonModal').addEventListener('click', function (e) {
        if (e.target === this) closeBonPanel();
    });
</script>
@endsection
