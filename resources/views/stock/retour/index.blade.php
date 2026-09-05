@extends('layouts.dashboard')

@section('title', 'Retour')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86); color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(94,200,179,.35); }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-size:.9rem; }
    .alert-ok { background:rgba(20,100,60,.25); border:1px solid rgba(100,255,160,.35); color:#b4ffd0; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-size:.9rem; }
    .form-panel { border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); padding:1.15rem 1.25rem 1.25rem; margin-bottom:1rem; }
    .form-row { display:grid; gap:.65rem .75rem; align-items:end; }
    .form-row-bars { grid-template-columns:140px 120px 150px 110px minmax(140px,1.4fr) 90px minmax(140px,1.2fr) auto; }
    .filter-row { grid-template-columns:180px minmax(180px,240px) auto; margin-bottom:0; }
    .field label { display:block; font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:var(--gold-light); margin-bottom:.3rem; font-weight:600; }
    .field input,.field select { width:100%; padding:.55rem .65rem; border-radius:10px; border:1px solid rgba(94,200,179,.3); background:var(--bg-input); color:var(--text); font-family:inherit; font-size:.85rem; outline:none; }
    .field input:focus,.field select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(94,200,179,.12); }
    .field input[readonly],.field select:disabled { opacity:.75; cursor:not-allowed; }
    .field select option { background:#2d0006; }
    .field-action { display:flex; align-items:end; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(94,200,179,.18); background:var(--surface); }
    .data-table { width:100%; border-collapse:collapse; min-width:900px; }
    .empty-row td { text-align:center; color:var(--text-muted); padding:2rem; }
    .section-title { font-family:'Fraunces', serif; color:var(--gold); font-size:1.1rem; margin:1rem 0 .75rem; }
    .hint { color:var(--text-muted); font-size:.88rem; margin-bottom:1rem; }
    @media (max-width:1100px) {
        .form-row-bars { grid-template-columns:repeat(4,minmax(0,1fr)); }
        .field-action { grid-column:1 / -1; justify-content:flex-end; }
    }
    @media (max-width:700px) {
        .form-row-bars,.filter-row { grid-template-columns:1fr 1fr; }
    }
    @media (max-width:480px) {
        .form-row-bars,.filter-row { grid-template-columns:1fr; }
    }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Retour{{ $depotLabel ? ' — '.$depotLabel : '' }}</h2>
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

    @if ($canCreate)
    <form method="POST" action="{{ route('stock.retour.store') }}" id="retourForm" class="form-panel">
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

        <div class="form-row form-row-bars">
            <div class="field">
                <label for="field_date">Date</label>
                <input type="date" name="date_mouvement" id="field_date" value="{{ old('date_mouvement', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="field">
                <label for="field_numero">Retour N°</label>
                <input type="text" id="field_numero" value="{{ $nextNumero }}" readonly>
            </div>
            <div class="field">
                <label for="field_depot">Dépôt</label>
                <input type="text" id="field_depot" value="{{ $depotLabel }}" readonly>
            </div>
            <div class="field">
                <label for="field_ref">Réf</label>
                <input type="text" name="ref" id="field_ref" list="refCatalogue" value="{{ old('ref') }}">
            </div>
            <div class="field">
                <label for="field_designation">Désignation</label>
                <input type="text" name="designation" id="field_designation" list="desCatalogue" value="{{ old('designation') }}" required>
            </div>
            <div class="field">
                <label for="field_qte">Qté</label>
                <input type="number" step="0.01" min="0.01" name="qte" id="field_qte" value="{{ old('qte', 1) }}" required>
            </div>
            <div class="field">
                <label for="field_remarque">Remarque</label>
                <input type="text" name="remarque" id="field_remarque" value="{{ old('remarque') }}" required placeholder="Motif du retour">
            </div>
            <div class="field field-action">
                <button type="submit" class="btn btn-gold">Valider</button>
            </div>
        </div>
    </form>
    @else
        <p class="hint">Consultation des retours régionaux. Le stock reçu est visible dans <strong>Stock Dépôt → Dépôt Retour</strong>.</p>
    @endif

    <form method="GET" action="{{ route('stock.retour') }}" class="form-panel">
        <div class="form-row filter-row">
            <div class="field">
                <label for="filter_mois">Mois</label>
                <select name="mois" id="filter_mois" onchange="this.form.submit()">
                    @foreach ($moisOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filterMois === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="filter_depot">Dépôt</label>
                <select name="depot" id="filter_depot" onchange="this.form.submit()" @disabled($canCreate)>
                    @unless ($canCreate)
                        <option value="">Tous les dépôts</option>
                    @endunless
                    @foreach ($filterDepots as $key => $label)
                        <option value="{{ $key }}" @selected($filterDepot === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @if ($canCreate)
                    <input type="hidden" name="depot" value="{{ $filterDepot }}">
                @endif
            </div>
            <div class="field field-action">
                <a href="{{ route('stock.retour') }}" class="btn btn-ghost">Réinitialiser</a>
            </div>
        </div>
    </form>

    <h3 class="section-title">Liste des retours</h3>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Retour N°</th>
                    <th>Dépôt</th>
                    <th>Réf</th>
                    <th>Désignation</th>
                    <th>Qté</th>
                    <th>Remarque</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['numero'] }}</td>
                        <td>{{ $row['depot'] }}</td>
                        <td>{{ $row['ref'] }}</td>
                        <td>{{ $row['designation'] }}</td>
                        <td>{{ number_format($row['qte'], 2, ',', ' ') }}</td>
                        <td>{{ $row['remarque'] }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Aucun retour trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($canCreate)
<script>
    const references = @json($references);
    const stockDepot = @json($stockDepot);
    const refInput = document.getElementById('field_ref');
    const desInput = document.getElementById('field_designation');

    function productKey(ref, designation) {
        ref = (ref || '').trim();
        if (ref && ref !== '—') return 'r:' + ref.toLowerCase();
        return 'd:' + (designation || '').trim().toLowerCase();
    }

    function applyFromRef() {
        const ref = (refInput.value || '').trim();
        if (!ref) return;
        const hit = references.find(r => r.ref.toLowerCase() === ref.toLowerCase());
        if (hit) desInput.value = hit.designation;
    }

    function applyFromDes() {
        const designation = (desInput.value || '').trim();
        if (!designation) return;
        const hit = references.find(r => r.designation.toLowerCase() === designation.toLowerCase());
        if (hit && !refInput.value.trim()) refInput.value = hit.ref;
    }

    refInput.addEventListener('change', applyFromRef);
    refInput.addEventListener('blur', applyFromRef);
    desInput.addEventListener('change', applyFromDes);
    desInput.addEventListener('blur', applyFromDes);

    document.getElementById('retourForm').addEventListener('submit', e => {
        const remarque = (document.getElementById('field_remarque').value || '').trim();
        const designation = (desInput.value || '').trim();
        const qte = parseFloat(document.getElementById('field_qte').value) || 0;
        if (!remarque) {
            e.preventDefault();
            alert('Indiquez une remarque pour justifier le retour.');
            return;
        }
        if (!designation || qte <= 0) {
            e.preventDefault();
            alert('Désignation et quantité sont obligatoires.');
            return;
        }
        const key = productKey(refInput.value, designation);
        const available = stockDepot[key] ?? 0;
        if (available <= 0.0005) {
            e.preventDefault();
            alert('Impossible de retourner un article en rupture de stock.');
            return;
        }
        if (qte > available + 0.0005) {
            e.preventDefault();
            alert('Quantité supérieure au stock disponible du dépôt.');
        }
    });
</script>
@endif
@endsection
