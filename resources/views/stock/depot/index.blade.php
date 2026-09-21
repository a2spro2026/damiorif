@extends('layouts.dashboard')

@section('title', 'Stock Dépôt')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar {
        display:flex; align-items:flex-start; justify-content:space-between; gap:1rem;
        margin-bottom:1rem; flex-wrap:wrap;
    }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); margin:0; }
    .page-meta { font-size:.78rem; color:var(--text-muted); margin-top:.25rem; }
    .toolbar-actions { display:flex; gap:.55rem; flex-wrap:wrap; align-items:center; margin-left:auto; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.55rem 1rem; border-radius:10px; font-family:inherit; font-size:.84rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }

    .search-rail {
        display:flex; align-items:center; gap:.55rem; flex-wrap:wrap;
        width:100%; margin-bottom:.95rem; padding:.55rem .65rem;
        border-radius:14px; border:1px solid rgba(94,200,179,.22);
        background:linear-gradient(135deg, rgba(94,200,179,.08), rgba(0,0,0,.18));
        box-shadow:inset 0 1px 0 rgba(255,255,255,.04);
        transform:translateY(0); opacity:1;
        animation: searchRailIn .45s cubic-bezier(.22,1,.36,1) both;
    }
    @keyframes searchRailIn {
        from { opacity:0; transform:translateY(-10px) scale(.985); }
        to { opacity:1; transform:translateY(0) scale(1); }
    }
    .search-rail .filter-group {
        display:flex; align-items:center; gap:.4rem; flex:1 1 auto; min-width:0; flex-wrap:wrap;
    }
    .search-rail .chip {
        position:relative; display:flex; align-items:center; gap:.35rem;
        flex:1 1 140px; min-width:120px; max-width:220px;
        transition:transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }
    .search-rail .chip:focus-within {
        transform:translateY(-2px);
        box-shadow:0 8px 22px rgba(94,200,179,.18);
    }
    .search-rail .chip-ico {
        position:absolute; left:.7rem; width:15px; height:15px; color:var(--gold);
        pointer-events:none; opacity:.85;
        transition:transform .25s ease, color .25s ease;
    }
    .search-rail .chip:focus-within .chip-ico { transform:scale(1.08); color:#A8E6D8; }
    .search-rail select,
    .search-rail input[type="search"] {
        width:100%; padding:.55rem .7rem .55rem 2rem; border-radius:10px;
        border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text);
        font-family:inherit; font-size:.84rem; outline:none;
        transition:border-color .25s ease, box-shadow .25s ease, transform .25s ease;
    }
    .search-rail select { padding-left:.7rem; max-width:200px; flex:0 1 180px; }
    .search-rail input[type="search"]:focus,
    .search-rail select:focus {
        border-color:var(--gold); box-shadow:0 0 0 3px rgba(94,200,179,.14);
    }
    .search-rail .chip-depot { flex:0 1 180px; max-width:200px; }
    .search-rail .chip-depot select { padding-left:.7rem; }
    .search-actions { display:flex; align-items:center; gap:.4rem; margin-left:auto; }
    .icon-btn {
        width:38px; height:38px; border-radius:11px; border:1px solid rgba(94,200,179,.35);
        background:linear-gradient(145deg, rgba(94,200,179,.16), rgba(0,0,0,.2));
        color:var(--gold); display:inline-flex; align-items:center; justify-content:center;
        cursor:pointer; flex-shrink:0;
        transition:transform .22s cubic-bezier(.22,1,.36,1), background .22s ease, box-shadow .22s ease;
    }
    .icon-btn:hover {
        transform:translateY(-2px) scale(1.04);
        background:rgba(94,200,179,.24);
        box-shadow:0 8px 18px rgba(94,200,179,.22);
    }
    .icon-btn:active { transform:translateY(0) scale(.97); }
    .icon-btn.search-go {
        width:auto; padding:0 .95rem; gap:.4rem; font-size:.8rem; font-weight:700; color:var(--burgundy-deep);
        background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); border-color:transparent;
    }
    .icon-btn.search-go svg { width:15px; height:15px; color:inherit; }
    .icon-btn svg { width:17px; height:17px; }
    .icon-btn.clear-btn { opacity:.85; }
    .table-wrap {
        overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface);
        animation: tableIn .5s cubic-bezier(.22,1,.36,1) .08s both;
    }
    @keyframes tableIn {
        from { opacity:0; transform:translateY(12px); }
        to { opacity:1; transform:translateY(0); }
    }
    .data-table { width:100%; border-collapse:collapse; min-width:820px; }
    .data-table tbody tr[data-row] {
        transition:opacity .28s ease, transform .28s ease, background .2s ease;
    }
    .data-table tbody tr[data-row].is-hiding {
        opacity:0; transform:translateX(12px); pointer-events:none;
    }
    .data-table tbody tr[data-row]:hover td { background:rgba(94,200,179,.08); }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .qte-pos { color:#bbf7d0; font-weight:700; }
    .qte-neg { color:#fecaca; font-weight:700; }
    html[data-theme="light"] .search-rail {
        background:linear-gradient(135deg, rgba(94,200,179,.12), rgba(255,255,255,.9));
        border-color:rgba(15,23,42,.1);
    }
    html[data-theme="light"] .qte-pos { color:#15803d; }
    html[data-theme="light"] .qte-neg { color:#b91c1c; }
    @media (max-width:720px) {
        .search-rail .chip, .search-rail select { max-width:none; flex:1 1 100%; }
        .search-actions { width:100%; margin-left:0; }
        .icon-btn.search-go { flex:1; justify-content:center; }
    }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <div>
            <h2>Stock Dépôt{{ $lockedDepot ? ' — '.$depotLabel : '' }}</h2>
            <div class="page-meta">Recherche par dépôt, référence ou désignation</div>
        </div>
        <div class="toolbar-actions">
            @if (!empty($canAdjust))
                <a href="{{ route('stock.ajuster', ['depot' => $depot]) }}" class="btn btn-gold">Ajuster</a>
            @endif
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    <div class="search-rail" id="stockDepotFilters">
        <div class="filter-group">
            @if (count($depotOptions) > 1)
                <form method="GET" class="chip chip-depot">
                    <select name="depot" onchange="this.form.submit()" title="Dépôt" aria-label="Dépôt">
                        @foreach ($depotOptions as $key => $label)
                            <option value="{{ $key }}" @selected($depot === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
            <label class="chip">
                <svg class="chip-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h10M4 17h7"/></svg>
                <input type="search" data-filter="ref" placeholder="Réf" autocomplete="off">
            </label>
            <label class="chip">
                <svg class="chip-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16v12H4z"/><path d="M8 10h8M8 14h5"/></svg>
                <input type="search" data-filter="designation" placeholder="Désignation" autocomplete="off">
            </label>
        </div>
        <div class="search-actions">
            <button type="button" class="icon-btn clear-btn" id="stockDepotClearBtn" title="Effacer" aria-label="Effacer la recherche">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <button type="button" class="icon-btn search-go" id="stockDepotSearchBtn" title="Rechercher" aria-label="Rechercher">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M20 20l-3.5-3.5"/>
                </svg>
                Rechercher
            </button>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="stockDepotTable">
            <thead>
                <tr>
                    <th>Réf</th>
                    <th>Désignation</th>
                    <th>Qté actuelle</th>
                    <th>Qté sortie</th>
                    <th>Qté en stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockRows as $row)
                    <tr data-row
                        data-ref="{{ mb_strtolower($row['ref']) }}"
                        data-designation="{{ mb_strtolower($row['designation']) }}">
                        <td>{{ $row['ref'] }}</td>
                        <td>{{ $row['designation'] }}</td>
                        <td>{{ number_format($row['qte_actuelle'], 2, ',', ' ') }}</td>
                        <td>{{ number_format($row['qte_sortie'], 2, ',', ' ') }}</td>
                        <td class="{{ $row['qte_en_stock'] > 0 ? 'qte-pos' : ($row['qte_en_stock'] < 0 ? 'qte-neg' : '') }}">
                            {{ number_format($row['qte_en_stock'], 2, ',', ' ') }}
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Aucun stock pour ce dépôt.</td></tr>
                @endforelse
                <tr class="empty-row js-filter-empty" style="display:none;"><td colspan="5">Aucun résultat pour cette recherche.</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    (function () {
        var table = document.getElementById('stockDepotTable');
        if (!table) return;
        var tbody = table.querySelector('tbody');
        var inputs = document.querySelectorAll('#stockDepotFilters [data-filter]');
        var emptyRow = tbody.querySelector('.js-filter-empty');
        var searchBtn = document.getElementById('stockDepotSearchBtn');
        var clearBtn = document.getElementById('stockDepotClearBtn');

        function apply(animate) {
            var filters = {};
            inputs.forEach(function (input) {
                filters[input.getAttribute('data-filter')] = (input.value || '').trim().toLowerCase();
            });
            var visible = 0;
            var rows = tbody.querySelectorAll('tr[data-row]');
            rows.forEach(function (tr) {
                var ok = true;
                Object.keys(filters).forEach(function (key) {
                    if (!filters[key]) return;
                    var hay = (tr.getAttribute('data-' + key) || '').toLowerCase();
                    if (hay.indexOf(filters[key]) === -1) ok = false;
                });
                if (animate) {
                    if (ok) {
                        tr.classList.remove('is-hiding');
                        tr.style.display = '';
                        visible++;
                    } else {
                        tr.classList.add('is-hiding');
                        setTimeout(function () {
                            if (tr.classList.contains('is-hiding')) tr.style.display = 'none';
                        }, 260);
                    }
                } else {
                    tr.classList.remove('is-hiding');
                    tr.style.display = ok ? '' : 'none';
                    if (ok) visible++;
                }
            });
            var dataCount = rows.length;
            if (emptyRow) emptyRow.style.display = (dataCount && !visible) ? '' : 'none';
        }

        if (searchBtn) searchBtn.addEventListener('click', function () { apply(true); });
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                inputs.forEach(function (input) { input.value = ''; });
                apply(true);
                if (inputs[0]) inputs[0].focus();
            });
        }
        inputs.forEach(function (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    apply(true);
                }
            });
        });
    })();
</script>
@endsection
