@extends('layouts.dashboard')

@section('title', $title)

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--gold); letter-spacing:.04em; }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.15rem; border-radius:10px; font-family:inherit; font-size:.88rem; font-weight:700; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,#d4af37,#c9a45c 50%,#a8863f); color:var(--burgundy-deep); }
    .btn-ghost { background:rgba(0,0,0,.25); color:var(--gold-light); border-color:rgba(201,164,92,.35); }
    .btn-sm { padding:.45rem .75rem; font-size:.8rem; }
    .alert-error { background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35); color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-size:.9rem; }
    .table-wrap { overflow-x:auto; border-radius:14px; border:1px solid rgba(201,164,92,.18); background:rgba(0,0,0,.22); }
    .data-table { width:100%; border-collapse:collapse; min-width:900px; }
    .action-btns { display:flex; gap:.35rem; justify-content:center; }
    .icon-btn { width:32px; height:32px; border-radius:8px; border:1px solid rgba(201,164,92,.3); background:rgba(0,0,0,.3); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
    .icon-btn svg { width:15px; height:15px; }
    .icon-btn.danger:hover { color:#ff9a9a; border-color:rgba(255,100,100,.5); }
    .modal-backdrop { position:fixed; inset:0; background:rgba(10,0,2,.75); backdrop-filter:blur(6px); z-index:200; display:none; align-items:flex-start; justify-content:center; padding:1rem; overflow-y:auto; }
    .modal-backdrop.open { display:flex; }
    .modal-sheet { width:min(640px,100%); margin:1rem auto 2rem; background:linear-gradient(160deg,rgba(84,0,11,.98),rgba(45,0,6,.99)); border:1px solid rgba(201,164,92,.35); border-radius:18px; box-shadow:0 20px 60px rgba(0,0,0,.55); padding:1.35rem 1.5rem; }
    .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid rgba(201,164,92,.2); }
    .modal-header h3 { font-family:'Playfair Display',serif; color:var(--gold); font-size:1.2rem; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
    .form-grid .full { grid-column:1 / -1; }
    .field label { display:block; font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:var(--gold-light); margin-bottom:.3rem; font-weight:600; }
    .field input,.field select { width:100%; padding:.55rem .65rem; border-radius:10px; border:1px solid rgba(201,164,92,.3); background:rgba(0,0,0,.35); color:#fff; font-family:inherit; font-size:.85rem; outline:none; }
    .field select option { background:#2d0006; }
    .modal-footer { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.1rem; padding-top:1rem; border-top:1px solid rgba(201,164,92,.18); }
    .empty-row td { text-align:center; color:rgba(255,255,255,.45); padding:2rem; }
    @media (max-width:600px) { .form-grid { grid-template-columns:1fr; } }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>{{ $title }}</h2>
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
                    <th>Libellé</th>
                    <th>Dépôt</th>
                    <th>Saisi par</th>
                    <th>Montant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->date_charge?->format('d/m/Y') }}</td>
                        <td>{{ $item->libelle }}</td>
                        <td>{{ $depots[$item->depot] ?? ($item->depot ?: '—') }}</td>
                        <td>{{ $item->user_name ?: '—' }}</td>
                        <td>{{ number_format((float) $item->montant, 2, ',', ' ') }}</td>
                        <td>
                            <div class="action-btns">
                                <button type="button" class="icon-btn" title="Modifier" onclick='openEditModal(@json($item))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('charges.destroy', $item) }}" onsubmit="return confirm('Supprimer cette ligne ?');" style="display:inline;">
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
                    <tr class="empty-row"><td colspan="6">Aucune ligne enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop" id="chargeModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="modalTitle">Ajouter</h3>
            <button type="button" class="icon-btn" onclick="closeModal()" title="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="chargeForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="form-grid">
                <div class="field">
                    <label for="field_date">Date</label>
                    <input type="date" name="date_charge" id="field_date" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="field">
                    <label for="field_depot">Dépôt</label>
                    <select name="depot" id="field_depot" required @if($lockedDepot) disabled @endif>
                        @foreach ($depots as $value => $label)
                            <option value="{{ $value }}" @selected($lockedDepot === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($lockedDepot)
                        <input type="hidden" name="depot" value="{{ $lockedDepot }}">
                    @endif
                </div>
                <div class="field full">
                    <label for="field_libelle">Libellé</label>
                    <input type="text" name="libelle" id="field_libelle" required>
                </div>
                <div class="field">
                    <label for="field_montant">Montant</label>
                    <input type="number" step="0.01" min="0.01" name="montant" id="field_montant" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('chargeModal');
    const form = document.getElementById('chargeForm');
    const storeUrl = @json(route($type === 'depense' ? 'charges.etat_depenses.store' : 'charges.etat_charges.store'));
    const updateBase = @json(url('/charges'));
    const lockedDepot = @json($lockedDepot);
    const defaultType = @json($type);

    function openModal() { modal.classList.add('open'); }
    function closeModal() { modal.classList.remove('open'); }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Ajouter';
        form.action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_date').value = @json(now()->toDateString());
        document.getElementById('field_libelle').value = '';
        document.getElementById('field_montant').value = '';
        if (lockedDepot) document.getElementById('field_depot').value = lockedDepot;
        openModal();
    }

    function openEditModal(item) {
        document.getElementById('modalTitle').textContent = 'Modifier';
        form.action = updateBase + '/' + item.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('field_date').value = (item.date_charge || '').substring(0, 10);
        document.getElementById('field_libelle').value = item.libelle || '';
        document.getElementById('field_montant').value = item.montant || '';
        document.getElementById('field_depot').value = item.depot || lockedDepot || '';
        openModal();
    }

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    @if ($errors->any()) openCreateModal(); @endif
</script>
@endsection
