@extends('layouts.dashboard')

@section('title', 'Tableau de Bord')

@section('content')
<style>
    .fiche-page { padding:.75rem 1.25rem 1.25rem !important; margin-top:-.5rem; }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .page-toolbar h2 { font-family:'Fraunces', serif; font-size:1.35rem; color:var(--gold); letter-spacing:.04em; }

    .dash-grid {
        display:grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .75rem;
    }
    .dash-card {
        display:flex;
        flex-direction:column;
        gap:.55rem;
        border-radius: 14px;
        padding: .85rem .95rem;
        background:var(--surface);
        border: 1px solid rgba(94, 200, 179, 0.18);
        box-shadow: inset 0 1px 0 rgba(94, 200, 179, 0.08);
        min-height: 0;
    }
    .dash-card.dash-card-wide { grid-column: span 2; }
    .dash-card.dash-card-table { grid-column: span 2; }

    .dash-mini-table-wrap {
        overflow:auto;
        border-radius:12px;
        border:1px solid rgba(94,200,179,.18);
        background:var(--surface);
        flex:1;
    }
    .dash-mini-table {
        width:100%;
        border-collapse:separate;
        border-spacing:0;
        min-width:520px;
    }
    .dash-mini-table thead th {
        text-align:center !important;
        vertical-align:middle !important;
        padding:.55rem .4rem !important;
        font-size:.62rem !important;
        font-weight:700 !important;
        text-transform:uppercase !important;
        letter-spacing:.08em !important;
        color:#0B1020 !important;
        white-space:nowrap;
        background: linear-gradient(180deg, #A8E6D8 0%, #5EC8B3 48%, #2A9B86 100%) !important;
        border-bottom:2px solid rgba(27,36,56,.45) !important;
    }
    .dash-mini-table thead th:first-child { border-top-left-radius:11px; }
    .dash-mini-table thead th:last-child { border-top-right-radius:11px; }
    .dash-mini-table tbody td {
        text-align:center !important;
        vertical-align:middle !important;
        padding:.5rem .4rem;
        font-size:.78rem;
        color:var(--text-soft);
        border-bottom:1px solid rgba(94,200,179,.1);
        white-space:nowrap;
    }
    .dash-mini-table tbody tr:nth-child(even) td { background:rgba(27,36,56,.18); }
    .dash-mini-table tbody tr:hover td { background:rgba(94,200,179,.1) !important; }
    .badge-solde {
        display:inline-block;
        min-width:2.6rem;
        padding:.18rem .55rem;
        border-radius:999px;
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.04em;
        text-transform:uppercase;
    }
    .badge-solde.oui {
        color:#bbf7d0;
        background:rgba(34,197,94,.22);
        border:1px solid rgba(74,222,128,.55);
    }
    .badge-solde.non {
        color:#fecaca;
        background:rgba(239,68,68,.22);
        border:1px solid rgba(248,113,113,.55);
    }
    .empty-mini td {
        color:var(--text-muted) !important;
        padding:1.4rem .75rem !important;
    }

    .card-top {
        display:flex; align-items:center; justify-content:space-between; gap:.65rem;
    }
    .card-icon {
        width:36px; height:36px; border-radius:10px;
        display:inline-flex; align-items:center; justify-content:center;
        background: rgba(94, 200, 179, 0.12);
        border: 1px solid rgba(94, 200, 179, 0.35);
        color: var(--gold-light);
        flex-shrink:0;
    }
    .card-icon svg { width:17px; height:17px; }
    .card-title {
        font-size:.68rem; text-transform:uppercase; letter-spacing:.08em;
        color: var(--gold-light); font-weight:700;
    }
    .card-value {
        font-family:'Fraunces', serif;
        font-size: 1.35rem;
        font-weight:700;
        letter-spacing:.02em;
        color:var(--text);
        line-height:1.2;
        margin-top:auto;
    }
    .card-value span {
        font-size:.65rem; font-family:'Manrope', sans-serif;
        color: var(--gold-light); margin-left:.3rem; letter-spacing:.06em;
        text-transform:uppercase; font-weight:600;
    }

    .card-split {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:.45rem;
    }
    .card-split-item {
        padding:.45rem .55rem;
        border-radius:10px;
        background:var(--surface);
        border:1px solid rgba(94,200,179,.2);
    }
    .card-split-label {
        font-size:.6rem;
        text-transform:uppercase;
        letter-spacing:.08em;
        color: var(--gold-light);
        font-weight:700;
        margin-bottom:.2rem;
    }
    .card-split-value {
        font-family:'Fraunces', serif;
        font-size:1rem;
        font-weight:700;
        color:var(--text);
        line-height:1.2;
    }
    .card-split-value span {
        font-size:.55rem;
        font-family:'Manrope', sans-serif;
        color: var(--gold-light);
        margin-left:.2rem;
        letter-spacing:.05em;
        text-transform:uppercase;
        font-weight:600;
    }

    .card-select {
        width:100%;
        appearance:none; -webkit-appearance:none;
        padding:.5rem 1.85rem .5rem .65rem;
        border-radius:10px;
        border:1px solid rgba(94,200,179,.3);
        background:var(--bg-input)
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23e8d5a8' stroke-width='2.8'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right .7rem center;
        color:var(--text); font-family:inherit; font-size:.85rem; font-weight:600;
        outline:none; cursor:pointer;
    }
    .card-select:hover, .card-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(94,200,179,.12);
    }
    .card-select option { background:var(--bg-modal); color:var(--text); }

    .card-controls {
        display:flex; gap:.55rem; flex-wrap:wrap; align-items:center;
    }
    .card-controls .card-select { flex:1; min-width:140px; }
    .period-toggle {
        display:inline-flex; border-radius:10px; overflow:hidden;
        border:1px solid rgba(94,200,179,.35);
        background:var(--bg-input);
        flex-shrink:0;
    }
    .period-toggle button {
        border:0; background:transparent; color:var(--text-soft);
        font-family:inherit; font-size:.75rem; font-weight:700;
        letter-spacing:.05em; text-transform:uppercase;
        padding:.5rem .7rem; cursor:pointer;
    }
    .period-toggle button.active {
        background: linear-gradient(135deg, rgba(94,200,179,.4), rgba(94,200,179,.18));
        color: var(--gold-light);
    }
    .chart-wrap {
        height:130px;
        border-radius:12px;
        padding:.4rem .35rem .2rem;
        background:var(--surface);
        border:1px solid rgba(94,200,179,.15);
    }

    @media (max-width: 1200px) {
        .dash-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .dash-card.dash-card-wide,
        .dash-card.dash-card-table { grid-column: span 3; }
    }
    @media (max-width: 900px) {
        .dash-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dash-card.dash-card-wide,
        .dash-card.dash-card-table { grid-column: span 2; }
        .chart-wrap { height:120px; }
    }
    @media (max-width: 600px) {
        .dash-grid { grid-template-columns: 1fr; }
        .dash-card.dash-card-wide,
        .dash-card.dash-card-table { grid-column: span 1; }
    }
</style>

<div class="content-panel fiche-page">
    <div class="page-toolbar">
        <h2>Tableau de Bord</h2>
    </div>

    <div class="dash-grid">
        @unless ($isDepotUser ?? false)
        <article class="dash-card">
            <div class="card-top">
                <div class="card-title">Dépôt DamioRif</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
                </div>
            </div>
            <div class="card-value" id="valDamiorif">{{ number_format($stockDamiorif, 2, ',', ' ') }} <span>MAD</span></div>
        </article>
        @endunless

        <article class="dash-card">
            <div class="card-top">
                <div class="card-title">Dépôt</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
            </div>
            <select class="card-select" id="selStockDepot" onchange="updateCard('stock', this.value, 'valStockDepot')">
                <option value="">— Sélectionner un dépôt —</option>
                @foreach ($depots as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <div class="card-value" id="valStockDepot">0,00 <span>MAD</span></div>
        </article>

        <article class="dash-card">
            <div class="card-top">
                <div class="card-title">Caisse par Dépôt</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                </div>
            </div>
            <select class="card-select" id="selCaisse" onchange="updateCard('caisse', this.value, 'valCaisse')">
                <option value="">— Sélectionner un dépôt —</option>
                @foreach ($depots as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <div class="card-value" id="valCaisse">0,00 <span>MAD</span></div>
        </article>

        <article class="dash-card">
            <div class="card-top">
                <div class="card-title">Valeur Réglements</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </div>
            </div>
            <select class="card-select" id="selReglements" onchange="updateReglementsCard(this.value)">
                <option value="">— Sélectionner un dépôt —</option>
                @foreach ($depots as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <div class="card-value" id="valReglements">0,00 <span>MAD</span></div>
            <div class="card-split">
                <div class="card-split-item">
                    <div class="card-split-label">Valeur Chq</div>
                    <div class="card-split-value" id="valCheques">0,00 <span>MAD</span></div>
                </div>
                <div class="card-split-item">
                    <div class="card-split-label">Valeur Traite</div>
                    <div class="card-split-value" id="valTraites">0,00 <span>MAD</span></div>
                </div>
            </div>
        </article>

        <article class="dash-card">
            <div class="card-top">
                <div class="card-title">Charges par Dépôt</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h8"/></svg>
                </div>
            </div>
            <select class="card-select" id="selCharges" onchange="updateCard('charges', this.value, 'valCharges')">
                <option value="">— Sélectionner un dépôt —</option>
                @foreach ($depots as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <div class="card-value" id="valCharges">0,00 <span>MAD</span></div>
        </article>

        <article class="dash-card">
            <div class="card-top">
                <div class="card-title">Dépenses par Dépôt</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>
                </div>
            </div>
            <select class="card-select" id="selDepenses" onchange="updateCard('depenses', this.value, 'valDepenses')">
                <option value="">— Sélectionner un dépôt —</option>
                @foreach ($depots as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <div class="card-value" id="valDepenses">0,00 <span>MAD</span></div>
        </article>

        @unless ($isDepotUser ?? false)
        <article class="dash-card">
            <div class="card-top">
                <div class="card-title">Solde Fournisseurs</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="card-value" id="valSoldeFrns">{{ number_format($soldeFournisseurs, 2, ',', ' ') }} <span>MAD</span></div>
        </article>
        @endunless

        <article class="dash-card">
            <div class="card-top">
                <div class="card-title">Solde Clients</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
            </div>
            <select class="card-select" id="selClients" onchange="updateCard('clients', this.value, 'valSoldeClients')">
                <option value="">— Sélectionner un dépôt —</option>
                @foreach ($depots as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <div class="card-value" id="valSoldeClients">0,00 <span>MAD</span></div>
        </article>

        <article class="dash-card dash-card-wide">
            <div class="card-top">
                <div class="card-title">Évolution Ventes par Dépôt</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>
                </div>
            </div>
            <div class="card-controls">
                <select class="card-select" id="selVentesDepot" onchange="refreshVentesChart()">
                    @foreach ($depots as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="period-toggle" role="group" aria-label="Période">
                    <button type="button" id="btnPeriodMois" class="active" onclick="setVentesPeriod('mois')">Mois</button>
                    <button type="button" id="btnPeriodAnnee" onclick="setVentesPeriod('annee')">Année</button>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="ventesChart"></canvas>
            </div>
        </article>

        <article class="dash-card dash-card-table">
            <div class="card-top">
                <div class="card-title">5 Derniers Bons de Ventes</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8M16 17H8M10 9H8"/></svg>
                </div>
            </div>
            <div class="dash-mini-table-wrap">
                <table class="dash-mini-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>N° Bon</th>
                            <th>Nom Client</th>
                            <th>Montant</th>
                            <th>Paiement</th>
                            <th>Solde</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($derniersBonsVente as $bon)
                            @php
                                $montant = (float) $bon->montant;
                                $solde = (float) $bon->solde;
                                $paiement = round($montant - $solde, 2);
                            @endphp
                            <tr>
                                <td>{{ $bon->date_bon?->format('d/m/Y') }}</td>
                                <td>{{ $bon->numero_bon }}</td>
                                <td>{{ $bon->nom_client }}</td>
                                <td>{{ number_format($montant, 2, ',', ' ') }}</td>
                                <td>{{ number_format($paiement, 2, ',', ' ') }}</td>
                                <td>{{ number_format($solde, 2, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr class="empty-mini">
                                <td colspan="6">Aucun bon de vente</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="dash-card dash-card-table">
            <div class="card-top">
                <div class="card-title">5 Derniers Bon Commande</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
            </div>
            <div class="dash-mini-table-wrap">
                <table class="dash-mini-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Dépôt</th>
                            <th>N° BN</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($derniersBonsCommande as $cmd)
                            <tr>
                                <td>{{ $cmd['date']?->format('d/m/Y') }}</td>
                                <td>{{ $cmd['depot'] }}</td>
                                <td>{{ $cmd['numero'] }}</td>
                                <td>{{ number_format($cmd['quantite'], 2, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr class="empty-mini">
                                <td colspan="4">Aucun bon de commande</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        @unless ($isDepotUser ?? false)
        <article class="dash-card dash-card-table">
            <div class="card-top">
                <div class="card-title">5 Derniers Bons Achats</div>
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h6"/></svg>
                </div>
            </div>
            <div class="dash-mini-table-wrap">
                <table class="dash-mini-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>N° Bon</th>
                            <th>Fournisseur</th>
                            <th>Montant</th>
                            <th>Soldé</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($derniersBonsAchat as $bon)
                            @php $estSolde = (float) $bon->solde <= 0; @endphp
                            <tr>
                                <td>{{ $bon->date_bon?->format('d/m/Y') }}</td>
                                <td>{{ $bon->numero_bon }}</td>
                                <td>{{ $bon->nom_fournisseur }}</td>
                                <td>{{ number_format((float) $bon->montant, 2, ',', ' ') }}</td>
                                <td>
                                    <span class="badge-solde {{ $estSolde ? 'oui' : 'non' }}">
                                        {{ $estSolde ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-mini">
                                <td colspan="5">Aucun bon d'achat</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
        @endunless
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const dashData = {
        stock: @json($stockByDepot),
        caisse: @json($caisseByDepot),
        reglements: @json($reglementsByDepot),
        cheques: @json($chequesByDepot),
        traites: @json($traitesByDepot),
        charges: @json($chargesByDepot),
        depenses: @json($depensesByDepot),
        clients: @json($soldeClientsByDepot),
    };

    const ventesEvolution = @json($ventesEvolution);
    const ventesMonthLabels = @json($ventesMonthLabels);
    const ventesYearLabels = @json($ventesYearLabels);
    let ventesPeriod = 'mois';
    let ventesChart = null;

    function formatMad(n) {
        return (Math.round((parseFloat(n) || 0) * 100) / 100).toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function setValue(el, target) {
        el.innerHTML = formatMad(target) + ' <span>MAD</span>';
    }

    function updateCard(dataset, depotKey, valueId) {
        const el = document.getElementById(valueId);
        if (!depotKey) {
            el.innerHTML = '0,00 <span>MAD</span>';
            return;
        }
        const value = (dashData[dataset] && dashData[dataset][depotKey]) || 0;
        setValue(el, value);
    }

    function updateReglementsCard(depotKey) {
        const total = depotKey ? ((dashData.reglements && dashData.reglements[depotKey]) || 0) : 0;
        const chq = depotKey ? ((dashData.cheques && dashData.cheques[depotKey]) || 0) : 0;
        const traite = depotKey ? ((dashData.traites && dashData.traites[depotKey]) || 0) : 0;
        setValue(document.getElementById('valReglements'), total);
        setValue(document.getElementById('valCheques'), chq);
        setValue(document.getElementById('valTraites'), traite);
    }

    function setVentesPeriod(period) {
        ventesPeriod = period;
        document.getElementById('btnPeriodMois').classList.toggle('active', period === 'mois');
        document.getElementById('btnPeriodAnnee').classList.toggle('active', period === 'annee');
        refreshVentesChart();
    }

    function getVentesSeries() {
        const depot = document.getElementById('selVentesDepot').value;
        const pack = ventesEvolution[depot] || { mois: [], annee: [] };
        if (ventesPeriod === 'annee') {
            return { labels: ventesYearLabels, data: pack.annee || [] };
        }
        return { labels: ventesMonthLabels, data: pack.mois || [] };
    }

    function chartTheme() {
        const light = document.documentElement.getAttribute('data-theme') === 'light';
        return light ? {
            tick: '#64748B',
            grid: 'rgba(15, 23, 42, 0.08)',
            tooltipBg: 'rgba(255,255,255,.96)',
            tooltipTitle: '#0F766E',
            tooltipBody: '#0F172A',
            tooltipBorder: 'rgba(15, 118, 110, 0.35)',
            point: '#0F766E',
            line: '#0D9488',
            fill: 'rgba(13, 148, 136, 0.12)',
        } : {
            tick: 'rgba(248,250,252,.55)',
            grid: 'rgba(94,200,179,.1)',
            tooltipBg: 'rgba(11,16,32,.92)',
            tooltipTitle: '#A8E6D8',
            tooltipBody: '#fff',
            tooltipBorder: 'rgba(94,200,179,.4)',
            point: '#A8E6D8',
            line: '#5EC8B3',
            fill: 'rgba(94, 200, 179, 0.18)',
        };
    }

    function applyChartTheme() {
        if (!ventesChart) return;
        const t = chartTheme();
        const ds = ventesChart.data.datasets[0];
        ds.borderColor = t.line;
        ds.backgroundColor = t.fill;
        ds.pointBackgroundColor = t.point;
        ds.pointBorderColor = t.line;
        ventesChart.options.plugins.tooltip.backgroundColor = t.tooltipBg;
        ventesChart.options.plugins.tooltip.titleColor = t.tooltipTitle;
        ventesChart.options.plugins.tooltip.bodyColor = t.tooltipBody;
        ventesChart.options.plugins.tooltip.borderColor = t.tooltipBorder;
        ventesChart.options.scales.x.ticks.color = t.tick;
        ventesChart.options.scales.x.grid.color = t.grid;
        ventesChart.options.scales.y.ticks.color = t.tick;
        ventesChart.options.scales.y.grid.color = t.grid;
        ventesChart.update('none');
    }

    function refreshVentesChart() {
        const series = getVentesSeries();
        if (!ventesChart) return;
        ventesChart.data.labels = series.labels;
        ventesChart.data.datasets[0].data = series.data;
        ventesChart.update();
    }

    function initVentesChart() {
        const ctx = document.getElementById('ventesChart');
        if (!ctx || typeof Chart === 'undefined') return;
        const series = getVentesSeries();
        const t = chartTheme();

        ventesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: series.labels,
                datasets: [{
                    label: 'Ventes',
                    data: series.data,
                    borderColor: t.line,
                    backgroundColor: t.fill,
                    borderWidth: 2.5,
                    pointBackgroundColor: t.point,
                    pointBorderColor: t.line,
                    pointRadius: 3.5,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: t.tooltipBg,
                        titleColor: t.tooltipTitle,
                        bodyColor: t.tooltipBody,
                        borderColor: t.tooltipBorder,
                        borderWidth: 1,
                        callbacks: {
                            label: (ctx) => ' ' + formatMad(ctx.parsed.y) + ' MAD',
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: { color: t.tick, font: { size: 10, weight: '600' } },
                        grid: { color: t.grid },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: t.tick,
                            font: { size: 10 },
                            callback: (v) => formatMad(v),
                        },
                        grid: { color: t.grid },
                    },
                },
            },
        });

        window.addEventListener('damiorif-theme-change', applyChartTheme);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const first = @json(array_key_first($depots));
        [
            ['selStockDepot', 'stock', 'valStockDepot'],
            ['selCaisse', 'caisse', 'valCaisse'],
            ['selCharges', 'charges', 'valCharges'],
            ['selDepenses', 'depenses', 'valDepenses'],
            ['selClients', 'clients', 'valSoldeClients'],
        ].forEach(([selId, key, valId]) => {
            const sel = document.getElementById(selId);
            if (sel && first) {
                sel.value = first;
                updateCard(key, first, valId);
            }
        });
        const selReglements = document.getElementById('selReglements');
        if (selReglements && first) {
            selReglements.value = first;
            updateReglementsCard(first);
        }
        const elDamiorif = document.getElementById('valDamiorif');
        if (elDamiorif) setValue(elDamiorif, @json($stockDamiorif));
        const elSoldeFrns = document.getElementById('valSoldeFrns');
        if (elSoldeFrns) setValue(elSoldeFrns, @json($soldeFournisseurs));
        initVentesChart();
    });
</script>
@endsection
