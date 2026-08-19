@extends('layouts.dashboard')

@section('title', 'Utilisateurs')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar {
        display:flex; align-items:center; justify-content:space-between;
        gap:1rem; margin-bottom:.85rem; flex-wrap:wrap;
    }
    .page-toolbar h2 {
        font-family:'Fraunces', serif;
        font-size:1.35rem; color:var(--gold); letter-spacing:.04em;
    }
    .page-toolbar .page-meta {
        font-size:.78rem; color:var(--text-muted); margin-top:.2rem;
    }
    .toolbar-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
    .btn {
        display:inline-flex; align-items:center; gap:.45rem;
        padding:.65rem 1.15rem; border-radius:10px;
        font-family:inherit; font-size:.88rem; font-weight:700;
        cursor:pointer; border:1px solid transparent; text-decoration:none;
        transition:all .2s ease;
    }
    .btn-gold {
        background:linear-gradient(135deg,#7DD3C0,#5EC8B3 50%,#2A9B86);
        color:var(--burgundy-deep); box-shadow:0 4px 16px rgba(94,200,179,.3);
    }
    .btn-gold:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(94,200,179,.4); }
    .btn-ghost {
        background:rgba(0,0,0,.25); color:var(--gold-light);
        border-color:rgba(94,200,179,.35);
    }
    .btn-ghost:hover { background:rgba(94,200,179,.12); border-color:var(--gold); }
    .alert-success {
        background:rgba(40,120,70,.25); border:1px solid rgba(80,200,120,.35);
        color:#b8f0c8; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-size:.9rem;
    }
    .alert-error {
        background:rgba(140,20,30,.25); border:1px solid rgba(255,100,100,.35);
        color:#ffb4b4; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-size:.9rem;
    }

    .table-wrap {
        overflow-x:auto; border-radius:14px;
        border:1px solid rgba(94,200,179,.22); background:var(--surface);
        box-shadow:inset 0 1px 0 rgba(94,200,179,.08);
    }
    .users-table {
        width:100%; border-collapse:separate; border-spacing:0;
        min-width:1180px;
    }
    .users-table thead th {
        text-align:center !important; vertical-align:middle !important;
        padding:.85rem .55rem !important;
        font-size:.68rem !important; font-weight:700 !important;
        text-transform:uppercase !important; letter-spacing:.1em !important;
        color:#2d0006 !important; white-space:nowrap;
        background:linear-gradient(180deg,#A8E6D8 0%,#5EC8B3 48%,#2A9B86 100%) !important;
        border-bottom:2px solid rgba(84,0,11,.45) !important;
    }
    .users-table thead th:first-child { border-top-left-radius:12px; }
    .users-table thead th:last-child { border-top-right-radius:12px; }
    .users-table tbody td {
        vertical-align:middle !important;
        padding:.8rem .55rem;
        font-size:.84rem;
        color:var(--text);
        border-bottom:1px solid rgba(94,200,179,.1);
    }
    .users-table tbody tr:nth-child(even) td { background:rgba(84,0,11,.18); }
    .users-table tbody tr:hover td { background:rgba(94,200,179,.1) !important; }

    .users-table .col-center { text-align:center !important; }
    .users-table .col-left { text-align:left !important; padding-left:.85rem !important; }

    .user-cell {
        display:flex; align-items:center; gap:.7rem; min-width:0;
    }
    .user-avatar {
        width:36px; height:36px; border-radius:50%; flex-shrink:0;
        display:inline-flex; align-items:center; justify-content:center;
        font-size:.72rem; font-weight:800; letter-spacing:.04em;
        color:var(--burgundy-deep);
        background:linear-gradient(145deg,#A8E6D8,#5EC8B3);
        border:1px solid rgba(94,200,179,.55);
        box-shadow:0 0 0 2px rgba(94,200,179,.12);
    }
    .user-meta { min-width:0; }
    .user-name {
        font-weight:700; color:var(--text); line-height:1.2;
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;
    }
    .user-sub {
        font-size:.7rem; color:var(--text-muted); margin-top:.15rem;
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;
    }

    .users-table .badge-statut {
        display:inline-flex; align-items:center; justify-content:center;
        padding:.28rem .7rem; border-radius:999px;
        font-size:.68rem; font-weight:800; letter-spacing:.05em;
        text-transform:uppercase; white-space:nowrap;
        background:rgba(94,200,179,.14);
        border:1px solid rgba(94,200,179,.4);
        color:var(--gold-light);
    }
    .users-table .badge-statut.directeur { background:rgba(94,200,179,.28); border-color:rgba(94,200,179,.65); color:#fff; }
    .users-table .badge-statut.gerant { background:rgba(96,165,250,.18); border-color:rgba(96,165,250,.45); color:#bfdbfe; }
    .users-table .badge-statut.facturation { background:rgba(52,211,153,.16); border-color:rgba(52,211,153,.45); color:#a7f3d0; }
    .users-table .badge-statut.magasinier { background:rgba(251,191,36,.16); border-color:rgba(251,191,36,.45); color:#fde68a; }
    .users-table .badge-statut.depot_tanger,
    .users-table .badge-statut.depot_nador,
    .users-table .badge-statut.depot_tetouan,
    .users-table .badge-statut.depot_houcima,
    .users-table .badge-statut.depot_belkciri {
        background:rgba(192,132,252,.16); border-color:rgba(192,132,252,.45); color:#e9d5ff;
    }

    .login-pill {
        display:inline-flex; align-items:center; gap:.35rem;
        padding:.28rem .65rem; border-radius:8px;
        background:var(--bg-input); border:1px solid rgba(94,200,179,.22);
        font-family:ui-monospace, Consolas, monospace;
        font-size:.78rem; color:var(--gold-light);
    }
    .pwd-mask {
        letter-spacing:.18em; color:var(--text-muted); font-size:.85rem;
    }

    .auth-cell { min-width:220px; max-width:280px; }
    .auth-chips {
        display:flex; flex-wrap:wrap; gap:.28rem;
        justify-content:flex-start !important; margin-inline:0 !important;
    }
    .auth-chip {
        font-size:.62rem; padding:.18rem .45rem; border-radius:6px;
        background:rgba(94,200,179,.1); border:1px solid rgba(94,200,179,.28);
        color:var(--gold-light); white-space:nowrap; font-weight:600;
    }
    .auth-chip.more {
        background:rgba(255,255,255,.06); border-color:rgba(255,255,255,.15);
        color:var(--text-soft);
    }
    .auth-empty { font-size:.78rem; color:var(--text-muted); }

    .action-btns {
        display:flex; justify-content:center; align-items:center; gap:.35rem;
    }
    .icon-btn {
        width:32px; height:32px; border-radius:8px;
        border:1px solid rgba(94,200,179,.3); background:var(--bg-input);
        color:var(--gold); display:inline-flex; align-items:center; justify-content:center;
        cursor:pointer; transition:all .2s ease;
    }
    .icon-btn:hover { background:rgba(94,200,179,.18); }
    .icon-btn.danger:hover { border-color:rgba(255,100,100,.5); color:#ff9a9a; }
    .icon-btn svg { width:15px; height:15px; }

    .modal-backdrop {
        position:fixed; inset:0; background:rgba(7,11,20,.72); backdrop-filter:blur(6px);
        z-index:200; display:none; align-items:center; justify-content:center; padding:1rem;
    }
    .modal-backdrop.open { display:flex; }
    .modal-sheet {
        width:min(860px,100%); max-height:92vh; overflow-y:auto;
        background:linear-gradient(160deg,rgba(84,0,11,.97),rgba(45,0,6,.98));
        border:1px solid rgba(94,200,179,.35); border-radius:18px;
        box-shadow:0 20px 60px rgba(0,0,0,.55), 0 0 40px rgba(94,200,179,.12);
        padding:1.5rem;
    }
    .modal-header {
        display:flex; align-items:center; justify-content:space-between;
        margin-bottom:1.25rem; padding-bottom:.85rem;
        border-bottom:1px solid rgba(94,200,179,.2);
    }
    .modal-header h3 { font-family:'Fraunces', serif; color:var(--gold); font-size:1.25rem; }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.9rem 1rem; }
    .form-grid .full { grid-column:1 / -1; }
    .field label {
        display:block; font-size:.72rem; text-transform:uppercase; letter-spacing:.07em;
        color:var(--gold-light); margin-bottom:.4rem; font-weight:600;
    }
    .field input, .field select {
        width:100%; padding:.7rem .85rem; border-radius:10px;
        border:1px solid rgba(94,200,179,.3); background:var(--bg-input);
        color:var(--text); font-family:inherit; font-size:.92rem; outline:none;
    }
    .field input:focus, .field select:focus {
        border-color:var(--gold); box-shadow:0 0 0 3px rgba(94,200,179,.15);
    }
    .field input:disabled, .field input[readonly], .field select:disabled { opacity:.7; cursor:not-allowed; }
    .field select option { background:#2d0006; }
    .auth-box {
        border:1px solid rgba(94,200,179,.22); border-radius:12px;
        padding:.9rem 1rem; background:var(--surface);
    }
    .auth-select-all {
        display:flex; align-items:center; justify-content:space-between; gap:.75rem;
        padding:.55rem .7rem; margin-bottom:.85rem;
        border-radius:10px;
        background:rgba(94,200,179,.1);
        border:1px solid rgba(94,200,179,.3);
    }
    .auth-select-all .check-item {
        font-weight:800; color:var(--gold-light); text-transform:uppercase;
        letter-spacing:.08em; font-size:.82rem;
    }
    .auth-select-all .auth-hint {
        font-size:.72rem; color:var(--text-muted);
    }
    .auth-section { margin-bottom:.75rem; }
    .auth-section:last-child { margin-bottom:0; }
    .auth-section-title {
        display:flex; align-items:center; gap:.55rem;
        font-weight:700; color:var(--gold-light); margin-bottom:.4rem; font-size:.9rem;
    }
    .auth-children {
        display:flex; flex-wrap:wrap; gap:.55rem 1rem;
        padding-left:1.6rem; margin-top:.35rem;
    }
    .check-item {
        display:inline-flex; align-items:center; gap:.4rem;
        font-size:.85rem; color:var(--text-soft); cursor:pointer;
    }
    .check-item input { width:15px; height:15px; accent-color:var(--gold); }
    .modal-footer {
        display:flex; justify-content:flex-end; gap:.65rem;
        margin-top:1.25rem; padding-top:1rem; border-top:1px solid rgba(94,200,179,.18);
    }
    .empty-row td { text-align:center !important; color:var(--text-muted); padding:2.2rem !important; }
    @media (max-width:700px) { .form-grid { grid-template-columns:1fr; } }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <div>
            <h2>Utilisateurs</h2>
            <div class="page-meta">{{ $users->count() }} compte{{ $users->count() > 1 ? 's' : '' }} enregistré{{ $users->count() > 1 ? 's' : '' }}</div>
        </div>
        <div class="toolbar-actions">
            <button type="button" class="btn btn-gold" onclick="openCreateModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Ajouter
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Fermer</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="table-wrap">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>CIN</th>
                    <th>Contact</th>
                    <th>Statut</th>
                    <th>Login</th>
                    <th>Mot de passe</th>
                    <th>Autorisations</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php
                        $authLabels = \App\Support\AppMenus::autorisationLabels($user->autorisations ?? []);
                        $visibleAuths = array_slice($authLabels, 0, 3);
                        $extraAuths = max(0, count($authLabels) - 3);
                        $initials = collect(preg_split('/\s+/', trim((string) $user->name)))
                            ->filter()
                            ->take(2)
                            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                            ->implode('');
                        if ($initials === '') {
                            $initials = '?';
                        }
                        $statutKey = $user->statut ?: '';
                        $statutLabel = $statuts[$user->statut] ?? $user->statut;
                    @endphp
                    <tr>
                        <td class="col-center">{{ $user->created_at?->format('d/m/Y') ?: '—' }}</td>
                        <td class="col-center"><strong>{{ $user->displayId() }}</strong></td>
                        <td class="col-left">
                            <div class="user-cell">
                                <span class="user-avatar">{{ $initials }}</span>
                                <div class="user-meta">
                                    <div class="user-name" title="{{ $user->name }}">{{ $user->name }}</div>
                                    <div class="user-sub">{{ $statutLabel }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="col-center">{{ $user->cin ?: '—' }}</td>
                        <td class="col-center">{{ $user->contact ?: '—' }}</td>
                        <td class="col-center">
                            <span class="badge-statut {{ $statutKey }}">{{ $statutLabel }}</span>
                        </td>
                        <td class="col-center">
                            <span class="login-pill">{{ $user->username }}</span>
                        </td>
                        <td class="col-center"><span class="pwd-mask">••••••••</span></td>
                        <td class="col-left auth-cell">
                            @if (count($authLabels))
                                <div class="auth-chips" title="{{ implode(' · ', $authLabels) }}">
                                    @foreach ($visibleAuths as $label)
                                        <span class="auth-chip">{{ $label }}</span>
                                    @endforeach
                                    @if ($extraAuths > 0)
                                        <span class="auth-chip more">+{{ $extraAuths }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="auth-empty">Aucune</span>
                            @endif
                        </td>
                        <td class="col-center">
                            <div class="action-btns">
                                <button type="button" class="icon-btn" title="Voir" onclick='openViewModal(@json($user))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button type="button" class="icon-btn" title="Modifier" onclick='openEditModal(@json($user))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('configuration.utilisateurs.destroy', $user) }}" onsubmit="return confirm('Supprimer cet utilisateur ?');" style="display:inline;">
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
                    <tr class="empty-row">
                        <td colspan="10">Aucun utilisateur enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Créer / Modifier / Voir --}}
<div class="modal-backdrop" id="userModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h3 id="modalTitle">Ajouter un utilisateur</h3>
            <button type="button" class="icon-btn" onclick="closeModal()" title="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" id="userForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="form-grid">
                <div class="field">
                    <label>Date</label>
                    <input type="text" id="field_date" value="{{ now()->format('d/m/Y') }}" readonly>
                </div>
                <div class="field">
                    <label>ID</label>
                    <input type="text" id="field_id" value="Auto" readonly>
                </div>
                <div class="field full">
                    <label for="field_name">Nom Complet</label>
                    <input type="text" name="name" id="field_name" required>
                </div>
                <div class="field">
                    <label for="field_cin">CIN</label>
                    <input type="text" name="cin" id="field_cin">
                </div>
                <div class="field">
                    <label for="field_contact">Contact</label>
                    <input type="text" name="contact" id="field_contact">
                </div>
                <div class="field">
                    <label for="field_username">Login</label>
                    <input type="text" name="username" id="field_username" required>
                </div>
                <div class="field">
                    <label for="field_password">Mot de Passe</label>
                    <input type="text" name="password" id="field_password">
                </div>
                <div class="field full">
                    <label for="field_statut">Statut</label>
                    <select name="statut" id="field_statut" required>
                        @foreach ($statuts as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field full">
                    <label>Autorisations</label>
                    <div class="auth-box" id="authBox">
                        <div class="auth-select-all">
                            <label class="check-item">
                                <input type="checkbox" id="authSelectAll" onclick="toggleSelectAllAuth(this)">
                                Tout
                            </label>
                            <span class="auth-hint">Sélectionner / désélectionner toutes les autorisations</span>
                        </div>
                        @foreach ($autorisations as $key => $section)
                            <div class="auth-section">
                                <label class="auth-section-title check-item">
                                    <input type="checkbox" name="autorisations[]" value="{{ $key }}" class="auth-check" data-section="{{ $key }}">
                                    {{ $section['label'] }}
                                </label>
                                @if (!empty($section['children']))
                                    <div class="auth-children">
                                        @foreach ($section['children'] as $childKey => $childLabel)
                                            <label class="check-item">
                                                <input type="checkbox" name="autorisations[]" value="{{ $childKey }}" class="auth-check" data-parent="{{ $key }}">
                                                {{ $childLabel }}
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="modal-footer" id="modalFooter">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn btn-gold" id="submitBtn">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const storeUrl = @json(route('configuration.utilisateurs.store'));
    const updateUrlTemplate = @json(url('/configuration/utilisateurs'));

    function openModal() {
        modal.classList.add('open');
    }

    function closeModal() {
        modal.classList.remove('open');
        setFormReadonly(false);
    }

    function clearAuthChecks() {
        document.querySelectorAll('.auth-check').forEach(el => el.checked = false);
        syncSelectAllAuth();
    }

    function setAuthChecks(list) {
        clearAuthChecks();
        (list || []).forEach(key => {
            const el = document.querySelector('.auth-check[value="' + key + '"]');
            if (el) el.checked = true;
        });
        syncSelectAllAuth();
    }

    function toggleSelectAllAuth(master) {
        document.querySelectorAll('.auth-check').forEach(el => {
            if (!el.disabled) el.checked = master.checked;
        });
        master.indeterminate = false;
    }

    function syncSelectAllAuth() {
        const master = document.getElementById('authSelectAll');
        if (!master) return;
        const checks = Array.from(document.querySelectorAll('.auth-check'));
        const enabled = checks.filter(el => !el.disabled);
        const checked = enabled.filter(el => el.checked);
        master.checked = enabled.length > 0 && checked.length === enabled.length;
        master.indeterminate = checked.length > 0 && checked.length < enabled.length;
    }

    document.querySelectorAll('.auth-check').forEach(el => {
        el.addEventListener('change', syncSelectAllAuth);
    });

    function setFormReadonly(readonly) {
        form.querySelectorAll('input, select').forEach(el => {
            if (el.id === 'field_date' || el.id === 'field_id') return;
            if (el.type === 'hidden') return;
            el.disabled = readonly;
        });
        const master = document.getElementById('authSelectAll');
        if (master) master.disabled = readonly;
        document.getElementById('modalFooter').style.display = readonly ? 'none' : 'flex';
        document.getElementById('submitBtn').style.display = readonly ? 'none' : 'inline-flex';
        syncSelectAllAuth();
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Ajouter un utilisateur';
        form.action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_date').value = @json(now()->format('d/m/Y'));
        document.getElementById('field_id').value = 'Auto';
        document.getElementById('field_name').value = '';
        document.getElementById('field_cin').value = '';
        document.getElementById('field_contact').value = '';
        document.getElementById('field_username').value = '';
        document.getElementById('field_password').value = '';
        document.getElementById('field_password').required = true;
        document.getElementById('field_password').placeholder = '';
        document.getElementById('field_statut').value = 'magasinier';
        clearAuthChecks();
        setFormReadonly(false);
        openModal();
    }

    function formatUserId(id) {
        const n = parseInt(id, 10);
        if (!n) return 'Auto';
        return 'ID' + String(n).padStart(4, '0');
    }

    function fillForm(user) {
        const created = user.created_at ? new Date(user.created_at) : null;
        document.getElementById('field_date').value = created
            ? created.toLocaleDateString('fr-FR')
            : '—';
        document.getElementById('field_id').value = formatUserId(user.id);
        document.getElementById('field_name').value = user.name || '';
        document.getElementById('field_cin').value = user.cin || '';
        document.getElementById('field_contact').value = user.contact || '';
        document.getElementById('field_username').value = user.username || '';
        document.getElementById('field_password').value = user.mot_de_passe || '';
        document.getElementById('field_statut').value = user.statut || 'magasinier';
        setAuthChecks(user.autorisations || []);
    }

    function openEditModal(user) {
        document.getElementById('modalTitle').textContent = 'Modifier utilisateur';
        form.action = updateUrlTemplate + '/' + user.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('field_password').required = false;
        document.getElementById('field_password').placeholder = 'Laisser vide pour ne pas changer';
        fillForm(user);
        setFormReadonly(false);
        openModal();
    }

    function openViewModal(user) {
        document.getElementById('modalTitle').textContent = 'Détail utilisateur';
        form.action = '#';
        fillForm(user);
        setFormReadonly(true);
        openModal();
    }

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
</script>
@endsection
