@extends('layouts.dashboard')

@section('title', 'Fiche Fournisseur')

@section('content')
<style>
    .page-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.85rem;
        flex-wrap: wrap;
    }
    .page-toolbar h2 {
        font-family: 'Fraunces', serif;
        font-size: 1.35rem;
        color: var(--gold);
        letter-spacing: 0.04em;
    }
    .toolbar-actions { display: flex; gap: 0.65rem; flex-wrap: wrap; }
    .btn {
        display: inline-flex; align-items: center; gap: 0.45rem;
        padding: 0.65rem 1.15rem; border-radius: 10px;
        font-family: inherit; font-size: 0.88rem; font-weight: 700;
        cursor: pointer; border: 1px solid transparent; text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-gold {
        background: linear-gradient(135deg, #7DD3C0, #5EC8B3 50%, #2A9B86);
        color: var(--burgundy-deep);
        box-shadow: 0 4px 16px rgba(94, 200, 179, 0.3);
    }
    .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(94, 200, 179, 0.4); }
    .btn-ghost {
        background: rgba(0, 0, 0, 0.25); color: var(--gold-light);
        border-color: rgba(94, 200, 179, 0.35);
    }
    .btn-ghost:hover { background: rgba(94, 200, 179, 0.12); border-color: var(--gold); }
    .alert-error {
        background: rgba(140, 20, 30, 0.25); border: 1px solid rgba(255, 100, 100, 0.35);
        color: #ffb4b4; padding: 0.75rem 1rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem;
    }
    .fiche-page {
        padding: 0.75rem 1.25rem 1.25rem !important;
        margin-top: -0.5rem;
    }
    .table-wrap {
        overflow-x: auto; border-radius: 14px;
        border: 1px solid rgba(94, 200, 179, 0.18); background:var(--surface);
    }
    .data-table { width: 100%; border-collapse: collapse; min-width: 1100px; }
    .action-btns { display: flex; gap: 0.4rem; }
    .icon-btn {
        width: 34px; height: 34px; border-radius: 9px;
        border: 1px solid rgba(94, 200, 179, 0.3); background:var(--bg-input);
        color: var(--gold); display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s ease;
    }
    .icon-btn:hover { background: rgba(94, 200, 179, 0.18); box-shadow: 0 0 12px rgba(94, 200, 179, 0.2); }
    .icon-btn.danger:hover { border-color: rgba(255,100,100,0.5); color: #ff9a9a; background: rgba(140,20,30,0.35); }
    .icon-btn svg { width: 16px; height: 16px; }
    .modal-backdrop {
        position: fixed; inset: 0; background: rgba(7,11,20,0.72); backdrop-filter: blur(6px);
        z-index: 200; display: none; align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-backdrop.open { display: flex; }
    .modal-sheet {
        width: min(760px, 100%); max-height: 92vh; overflow-y: auto;
        background: linear-gradient(160deg, rgba(84,0,11,0.97), rgba(45,0,6,0.98));
        border: 1px solid rgba(94, 200, 179, 0.35); border-radius: 18px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.55), 0 0 40px rgba(94,200,179,0.12);
        padding: 1.5rem;
    }
    .modal-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.25rem; padding-bottom: 0.85rem;
        border-bottom: 1px solid rgba(94, 200, 179, 0.2);
    }
    .modal-header h3 { font-family: 'Fraunces', serif; color: var(--gold); font-size: 1.25rem; }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 0.9rem 1rem; }
    .form-grid .full { grid-column: 1 / -1; }
    .field label {
        display: block; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.07em;
        color: var(--gold-light); margin-bottom: 0.4rem; font-weight: 600;
    }
    .field input, .field select {
        width: 100%; padding: 0.7rem 0.85rem; border-radius: 10px;
        border: 1px solid rgba(94, 200, 179, 0.3); background:var(--bg-input);
        color:var(--text); font-family: inherit; font-size: 0.92rem; outline: none;
    }
    .field input:focus, .field select:focus {
        border-color: var(--gold); box-shadow: 0 0 0 3px rgba(94,200,179,0.15);
    }
    .field input:disabled, .field input[readonly], .field select:disabled { opacity: 0.7; cursor: not-allowed; }
    .field select option { background: #2d0006; }
    .field .hint { font-size: 0.72rem; color:var(--text-muted); margin-top: 0.3rem; }
    .modal-footer {
        display: flex; justify-content: flex-end; gap: 0.65rem;
        margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(94,200,179,0.18);
    }
    .empty-row td { text-align: center; color:var(--text-muted); padding: 2rem; }
    @media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; } }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Fiche Fournisseur</h2>
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
                    <th>Réf Frns</th>
                    <th>Nom Fournisseur</th>
                    <th>Nom Gérant</th>
                    <th>Contact</th>
                    <th>Ville</th>
                    <th>Type Régl</th>
                    <th>Bnq</th>
                    <th>Rib</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fournisseurs as $f)
                    <tr>
                        <td>{{ $f->date_fiche?->format('d/m/Y') ?? $f->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $f->ref_frns }}</td>
                        <td>{{ $f->nom_fournisseur }}</td>
                        <td>{{ $f->nom_gerant ?: '—' }}</td>
                        <td>{{ $f->contact ?: '—' }}</td>
                        <td>{{ $f->ville ?: '—' }}</td>
                        <td>{{ $typesReglement[$f->type_reglement] ?? ($f->type_reglement ?: '—') }}</td>
                        <td>{{ $f->banque ?: '—' }}</td>
                        <td style="font-family:monospace;letter-spacing:.04em;">{{ $f->rib }}</td>
                        <td>
                            <div class="action-btns">
                                <button type="button" class="icon-btn" title="Voir" onclick='openViewModal(@json($f))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button type="button" class="icon-btn" title="Modifier" onclick='openEditModal(@json($f))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('fournisseurs.fiche.destroy', $f) }}" onsubmit="return confirm('Supprimer ce fournisseur ?');" style="display:inline;">
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
                    <tr class="empty-row"><td colspan="10">Aucun fournisseur enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop" id="frnsModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="modalTitle">Ajouter un fournisseur</h3>
            <button type="button" class="icon-btn" onclick="closeModal()" title="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" id="frnsForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="form-grid">
                <div class="field">
                    <label>Date</label>
                    <input type="text" id="field_date" value="{{ now()->format('d/m/Y') }}" readonly>
                </div>
                <div class="field">
                    <label>Réf Frns</label>
                    <input type="text" id="field_ref" value="{{ $nextRef }}" readonly>
                </div>
                <div class="field full">
                    <label for="field_nom">Nom Fournisseur</label>
                    <input type="text" name="nom_fournisseur" id="field_nom" required>
                </div>
                <div class="field">
                    <label for="field_gerant">Nom Gérant</label>
                    <input type="text" name="nom_gerant" id="field_gerant">
                </div>
                <div class="field">
                    <label for="field_contact">Contact</label>
                    <input type="text" name="contact" id="field_contact">
                </div>
                <div class="field">
                    <label for="field_ville">Ville</label>
                    <input type="text" name="ville" id="field_ville">
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
                    <label for="field_banque">Bnq</label>
                    <input type="text" name="banque" id="field_banque">
                </div>
                <div class="field full">
                    <label for="field_rib">Rib</label>
                    <input type="text" name="rib" id="field_rib" maxlength="24" pattern="\d{24}" inputmode="numeric" required>
                    <div class="hint">24 chiffres obligatoires</div>
                </div>
            </div>

            <div class="modal-footer" id="modalFooter">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Fermer</button>
                <button type="submit" class="btn btn-gold" id="submitBtn">Valider</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('frnsModal');
    const form = document.getElementById('frnsForm');
    const storeUrl = @json(route('fournisseurs.fiche.store'));
    const updateBase = @json(url('/fournisseurs/fiche'));
    const nextRef = @json($nextRef);
    const today = @json(now()->format('d/m/Y'));

    function openModal() { modal.classList.add('open'); }
    function closeModal() {
        modal.classList.remove('open');
        setFormReadonly(false);
    }

    function setFormReadonly(readonly) {
        form.querySelectorAll('input, select').forEach(el => {
            if (el.id === 'field_date' || el.id === 'field_ref' || el.type === 'hidden') return;
            el.disabled = readonly;
        });
        document.getElementById('submitBtn').style.display = readonly ? 'none' : 'inline-flex';
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Ajouter un fournisseur';
        form.action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_date').value = today;
        document.getElementById('field_ref').value = nextRef;
        document.getElementById('field_nom').value = '';
        document.getElementById('field_gerant').value = '';
        document.getElementById('field_contact').value = '';
        document.getElementById('field_ville').value = '';
        document.getElementById('field_type').value = '';
        document.getElementById('field_banque').value = '';
        document.getElementById('field_rib').value = '';
        setFormReadonly(false);
        openModal();
    }

    function fillForm(f) {
        const d = f.date_fiche || f.created_at;
        document.getElementById('field_date').value = d
            ? new Date(d).toLocaleDateString('fr-FR')
            : '—';
        document.getElementById('field_ref').value = f.ref_frns || '';
        document.getElementById('field_nom').value = f.nom_fournisseur || '';
        document.getElementById('field_gerant').value = f.nom_gerant || '';
        document.getElementById('field_contact').value = f.contact || '';
        document.getElementById('field_ville').value = f.ville || '';
        document.getElementById('field_type').value = f.type_reglement || '';
        document.getElementById('field_banque').value = f.banque || '';
        document.getElementById('field_rib').value = f.rib || '';
    }

    function openEditModal(f) {
        document.getElementById('modalTitle').textContent = 'Modifier fournisseur';
        form.action = updateBase + '/' + f.id;
        document.getElementById('formMethod').value = 'PUT';
        fillForm(f);
        setFormReadonly(false);
        openModal();
    }

    function openViewModal(f) {
        document.getElementById('modalTitle').textContent = 'Détail fournisseur';
        form.action = '#';
        fillForm(f);
        setFormReadonly(true);
        openModal();
    }

    document.getElementById('field_rib').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 24);
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    @if ($errors->any())
        openCreateModal();
    @endif
</script>
@endsection
