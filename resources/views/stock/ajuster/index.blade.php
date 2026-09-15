@extends('layouts.dashboard')

@section('title', 'Ajuster Stock')

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
    .ajuster-top { flex:0 0 auto; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; align-items:center; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:.85rem; font-size:.9rem; }
    .alert-ok { background:rgba(20,100,60,.25); border:1px solid rgba(100,255,160,.35); color:#b4ffd0; padding:.75rem 1rem; border-radius:10px; margin-bottom:.85rem; font-size:.9rem; }
    .form-panel { border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); padding:1rem 1.15rem; margin-bottom:.75rem; }
    .form-row { display:grid; gap:.55rem .85rem; align-items:end; margin-bottom:.65rem; }
    .form-row-meta { grid-template-columns:160px 160px minmax(200px,1fr); }
    .form-row-remarque { grid-template-columns:1fr; margin-bottom:0; }
    .field label { display:block; font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:var(--gold-light); margin-bottom:.3rem; font-weight:600; }
    .field input,.field select,.field textarea { width:100%; padding:.55rem .65rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); font-family:inherit; font-size:.85rem; outline:none; }
    .field textarea { min-height:52px; resize:vertical; }
    .field input:focus,.field select:focus,.field textarea:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(94,200,179,.12); }
    .field input[readonly] { opacity:.75; cursor:not-allowed; }
    .field select option { background:#2d0006; }
    .hint { color:var(--text-muted); font-size:.82rem; margin:.55rem 0 0; }
    .list-wrap {
        flex:1 1 auto;
        min-height:0;
        border-radius:14px;
        border:1px solid rgba(94,200,179,.18);
        background:var(--surface);
        overflow-y:auto;
        overflow-x:hidden;
        -webkit-overflow-scrolling:touch;
    }
    .stock-list { list-style:none; margin:0; padding:0; }
    .stock-item {
        display:grid;
        grid-template-columns:110px minmax(0,1fr) auto;
        gap:.75rem 1rem;
        align-items:center;
        padding:.75rem 1rem;
        border-bottom:1px solid rgba(94,200,179,.12);
    }
    .stock-item:last-child { border-bottom:none; }
    .stock-item.changed { background:rgba(94,200,179,.08); }
    .stock-ref { font-weight:700; color:var(--gold); font-size:.88rem; }
    .stock-des { color:var(--text); font-size:.9rem; }
    .qty-box { display:flex; align-items:center; gap:.45rem; justify-self:end; }
    .qty-stock { min-width:4.2rem; text-align:center; font-weight:700; font-variant-numeric:tabular-nums; }
    .qty-stock.pos { color:#bbf7d0; }
    .qty-stock.neg { color:#fecaca; }
    .qty-input {
        width:5.5rem; padding:.4rem .45rem; border-radius:8px;
        border:1px solid rgba(94,200,179,.35); background:var(--bg-input);
        color:var(--text); font-family:inherit; font-size:.88rem; font-weight:700;
        text-align:center; font-variant-numeric:tabular-nums; outline:none;
    }
    .qty-input.pos { color:#bbf7d0; }
    .qty-input.neg { color:#fecaca; }
    .qty-delta { min-width:3.2rem; text-align:center; font-size:.78rem; color:var(--gold-light); font-weight:700; }
    .qty-delta.zero { opacity:.35; }
    .icon-btn {
        width:34px; height:34px; border-radius:8px;
        border:1px solid rgba(94,200,179,.35); background:var(--bg-input); color:var(--gold);
        display:inline-flex; align-items:center; justify-content:center; cursor:pointer;
    }
    .icon-btn:hover { background:rgba(94,200,179,.18); }
    .icon-btn svg { width:16px; height:16px; }
    .icon-btn.minus:hover { color:#ff9a9a; border-color:rgba(255,100,100,.5); }
    .form-footer {
        flex:0 0 auto;
        display:flex;
        justify-content:flex-end;
        gap:.65rem;
        margin-top:.75rem;
        padding-top:.75rem;
        border-top:1px solid rgba(94,200,179,.18);
        background:var(--surface);
    }
    .empty-msg { text-align:center; color:var(--text-muted); padding:2rem; }
    #ajusterForm { display:flex; flex-direction:column; flex:1 1 auto; min-height:0; }
    @media (max-width:720px) {
        .fiche-page { height:calc(100vh - 4.5rem); }
        .form-row-meta { grid-template-columns:1fr 1fr; }
        .stock-item { grid-template-columns:1fr; gap:.35rem; }
        .qty-box { justify-self:start; }
    }
    @media (max-width:520px) {
        .form-row-meta { grid-template-columns:1fr; }
    }
</style>

<div class="content-panel fiche-page">
    <div class="ajuster-top">
        <div class="page-toolbar">
            <h2>Ajuster Stock</h2>
            <div class="toolbar-actions">
                <a href="{{ route('stock.depot', ['depot' => $depot]) }}" class="btn btn-ghost">Stock Dépôt</a>
                <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif
        @if (session('success'))
            <div class="alert-ok">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('stock.ajuster') }}" class="form-panel" style="padding-bottom:.85rem;">
            <div class="form-row form-row-meta" style="margin-bottom:0;">
                <div class="field">
                    <label for="filter_depot">Dépôt</label>
                    <select name="depot" id="filter_depot" onchange="this.form.submit()">
                        @foreach ($depots as $key => $label)
                            <option value="{{ $key }}" @selected($depot === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <form method="POST" action="{{ route('stock.ajuster.store') }}" id="ajusterForm">
        @csrf
        <div class="ajuster-top form-panel">
            <div class="form-row form-row-meta">
                <div class="field">
                    <label for="field_date">Date</label>
                    <input type="date" name="date_mouvement" id="field_date" value="{{ old('date_mouvement', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="field">
                    <label for="field_numero">Ajustement N°</label>
                    <input type="text" id="field_numero" value="{{ $nextNumero }}" readonly>
                </div>
                <div class="field">
                    <label>Dépôt</label>
                    <input type="text" value="{{ $depotLabel }}" readonly>
                    <input type="hidden" name="depot" value="{{ $depot }}">
                </div>
            </div>
            <div class="form-row form-row-remarque">
                <div class="field">
                    <label for="field_remarque">Remarque</label>
                    <textarea name="remarque" id="field_remarque" required placeholder="Motif de l’ajustement…">{{ old('remarque') }}</textarea>
                </div>
            </div>
            <p class="hint">Saisissez la quantité manuellement ou utilisez <strong>−</strong> / <strong>+</strong>. Seules les lignes modifiées seront enregistrées.</p>
        </div>

        <div class="list-wrap">
            @if (count($stockRows) === 0)
                <p class="empty-msg">Aucun article en stock pour ce dépôt.</p>
            @else
                <ul class="stock-list" id="stockList">
                    @foreach ($stockRows as $i => $row)
                        <li class="stock-item" data-index="{{ $i }}" data-stock="{{ $row['qte_en_stock'] }}">
                            <div class="stock-ref">{{ $row['ref'] }}</div>
                            <div class="stock-des">{{ $row['designation'] }}</div>
                            <div class="qty-box">
                                <button type="button" class="icon-btn minus" title="Diminuer" onclick="bump({{ $i }}, -1)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg>
                                </button>
                                <input type="number"
                                       class="qty-input {{ $row['qte_en_stock'] > 0 ? 'pos' : ($row['qte_en_stock'] < 0 ? 'neg' : '') }}"
                                       step="0.01"
                                       data-qty
                                       value="{{ rtrim(rtrim(number_format($row['qte_en_stock'], 2, '.', ''), '0'), '.') }}"
                                       oninput="setQty({{ $i }}, this.value)">
                                <button type="button" class="icon-btn plus" title="Augmenter" onclick="bump({{ $i }}, 1)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                                </button>
                                <span class="qty-delta zero" data-delta>0</span>
                            </div>
                            <input type="hidden" data-ref value="{{ $row['ref'] }}">
                            <input type="hidden" data-des value="{{ $row['designation'] }}">
                            <input type="hidden" data-adj value="0">
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div id="hiddenLignes"></div>

        <div class="form-footer">
            <a href="{{ route('stock.depot', ['depot' => $depot]) }}" class="btn btn-ghost">Fermer</a>
            <button type="submit" class="btn btn-gold" @disabled(count($stockRows) === 0)>Valider</button>
        </div>
    </form>
</div>

<script>
    function fmt(n) {
        return (Math.round(n * 100) / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function applyAdj(item, adj) {
        const stock = parseFloat(item.dataset.stock) || 0;
        const adjInput = item.querySelector('[data-adj]');
        adjInput.value = adj;

        const newStock = stock + adj;
        const qtyEl = item.querySelector('[data-qty]');
        const deltaEl = item.querySelector('[data-delta]');
        if (document.activeElement !== qtyEl) {
            qtyEl.value = (Math.round(newStock * 100) / 100);
        }
        qtyEl.classList.toggle('pos', newStock > 0);
        qtyEl.classList.toggle('neg', newStock < 0);
        deltaEl.textContent = (adj > 0 ? '+' : '') + fmt(adj);
        deltaEl.classList.toggle('zero', Math.abs(adj) < 0.0005);
        item.classList.toggle('changed', Math.abs(adj) >= 0.0005);
    }

    function bump(index, step) {
        const item = document.querySelector('.stock-item[data-index="' + index + '"]');
        if (!item) return;
        const adjInput = item.querySelector('[data-adj]');
        const adj = (parseFloat(adjInput.value) || 0) + step;
        applyAdj(item, adj);
    }

    function setQty(index, raw) {
        const item = document.querySelector('.stock-item[data-index="' + index + '"]');
        if (!item) return;
        const stock = parseFloat(item.dataset.stock) || 0;
        const next = parseFloat(raw);
        if (Number.isNaN(next)) return;
        applyAdj(item, Math.round((next - stock) * 1000) / 1000);
    }

    document.getElementById('ajusterForm').addEventListener('submit', function (e) {
        const remarque = (document.getElementById('field_remarque').value || '').trim();
        if (!remarque) {
            e.preventDefault();
            alert('Indiquez une remarque pour justifier l\'ajustement.');
            return;
        }

        const box = document.getElementById('hiddenLignes');
        box.innerHTML = '';
        let n = 0;
        document.querySelectorAll('.stock-item').forEach(item => {
            const adj = parseFloat(item.querySelector('[data-adj]').value) || 0;
            if (Math.abs(adj) < 0.0005) return;
            const i = n++;
            const ref = item.querySelector('[data-ref]').value;
            const des = item.querySelector('[data-des]').value;
            box.insertAdjacentHTML('beforeend',
                `<input type="hidden" name="lignes[${i}][ref]" value="${ref.replace(/"/g, '&quot;')}">` +
                `<input type="hidden" name="lignes[${i}][designation]" value="${des.replace(/"/g, '&quot;')}">` +
                `<input type="hidden" name="lignes[${i}][qte]" value="${adj}">`
            );
        });

        if (n === 0) {
            e.preventDefault();
            alert('Ajustez au moins une quantité (saisie manuelle ou + / −).');
        }
    });
</script>
@endsection
