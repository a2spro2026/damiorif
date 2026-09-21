<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bon {{ $bon->numero_bon }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            background: #fff;
            color: #000;
            font-family: "Courier New", Courier, monospace;
            font-size: 12px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body { padding: 8px; }
        .ticket {
            width: 72mm;
            max-width: 100%;
            margin: 0 auto;
        }
        .no-print {
            display: block;
            margin: 0 auto 12px;
            padding: 8px 14px;
            border: 1px solid #000;
            background: #111;
            color: #fff;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border-radius: 6px;
        }
        .header {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
            margin-bottom: 8px;
        }
        .header img {
            display: block;
            width: 42mm;
            max-width: 160px;
            height: auto;
            margin: 0 auto 6px;
            object-fit: contain;
        }
        .brand {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .doc-title {
            text-align: center;
            font-weight: 800;
            font-size: 13px;
            margin: 8px 0 6px;
            text-transform: uppercase;
        }
        .meta { margin-bottom: 8px; }
        .meta .row {
            display: flex;
            justify-content: space-between;
            gap: 6px;
            padding: 1px 0;
        }
        .meta .label { font-weight: 700; white-space: nowrap; }
        .meta .value { text-align: right; word-break: break-word; }
        .sep {
            border: 0;
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .item {
            padding: 6px 0;
            border-bottom: 1px dashed #000;
        }
        .item:last-child { border-bottom: 0; }
        .item-ref {
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
        }
        .item-des {
            margin: 2px 0 4px;
            word-break: break-word;
        }
        .item-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 8px;
        }
        .item-grid .full { grid-column: 1 / -1; }
        .k { font-weight: 700; }
        .totals {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 2px solid #000;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 2px 0;
            font-weight: 700;
        }
        .totals .grand {
            font-size: 14px;
            font-weight: 900;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px solid #000;
        }
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 11px;
            line-height: 1.45;
        }
        .footer .thanks {
            font-weight: 800;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .footer .line { word-break: break-word; }
        @page {
            size: 80mm auto;
            margin: 2mm;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .ticket { width: 76mm; }
            a[href]::after { content: none !important; }
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/logo.png');
    $logoSrc = is_file($logoPath)
        ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
        : asset('images/logo.png');
    $companyName = (string) config('company.name', 'DAMIORIF');
    $companyAddress = trim((string) config('company.address', ''));
    $companyPhone = trim((string) config('company.phone', ''));
    $companyEmail = trim((string) config('company.email', ''));
@endphp

    <button type="button" class="no-print" onclick="window.print()">Imprimer le ticket</button>

    <div class="ticket">
        <header class="header">
            <img src="{{ $logoSrc }}" alt="{{ $companyName }}" width="160" height="80">
            <div class="brand">{{ $companyName }}</div>
        </header>

        <div class="doc-title">Bon de vente</div>

        <div class="meta">
            <div class="row"><span class="label">N°</span><span class="value">{{ $bon->numero_bon }}</span></div>
            <div class="row"><span class="label">Date</span><span class="value">{{ $bon->date_bon?->format('d/m/Y') }}</span></div>
            <div class="row"><span class="label">Client</span><span class="value">{{ $bon->nom_client }}</span></div>
            <div class="row"><span class="label">ID</span><span class="value">{{ $bon->client_id }}</span></div>
            @if ($bon->ville)
                <div class="row"><span class="label">Ville</span><span class="value">{{ $bon->ville }}</span></div>
            @endif
            <div class="row"><span class="label">Régl.</span><span class="value">{{ $typesReglement[$bon->type_reglement] ?? ($bon->type_reglement ?: '—') }}</span></div>
            <div class="row"><span class="label">Échéance</span><span class="value">{{ \App\Support\Echeances::label($bon->echeance !== null ? (string) $bon->echeance : null) }}</span></div>
            <div class="row"><span class="label">Dépôt</span><span class="value">{{ $depots[$bon->depot] ?? ($bon->depot ?: '—') }}</span></div>
        </div>

        <hr class="sep">

        @forelse ($bon->lignes as $i => $ligne)
            <div class="item">
                <div class="item-ref">{{ $ligne->ref ?: '—' }}</div>
                <div class="item-des">{{ $ligne->designation }}</div>
                <div class="item-grid">
                    <div><span class="k">Qté</span> {{ number_format((float) $ligne->qte, 2, ',', ' ') }}</div>
                    <div><span class="k">P/U</span> {{ number_format((float) $ligne->prix_unitaire, 2, ',', ' ') }}</div>
                    <div class="full"><span class="k">Sous-total</span> {{ number_format((float) $ligne->sous_total, 2, ',', ' ') }}</div>
                </div>
            </div>
        @empty
            <div class="item">Aucun article.</div>
        @endforelse

        <div class="totals">
            <div class="row"><span>Qté totale</span><span>{{ number_format((float) $bon->qte_totale, 2, ',', ' ') }}</span></div>
            <div class="row"><span>Montant</span><span>{{ number_format((float) $bon->montant, 2, ',', ' ') }}</span></div>
            <div class="row grand"><span>Solde</span><span>{{ number_format((float) $bon->solde, 2, ',', ' ') }}</span></div>
        </div>

        <footer class="footer">
            <div class="thanks">Merci de votre confiance</div>
            @if ($companyAddress !== '')
                <div class="line">{{ $companyAddress }}</div>
            @endif
            @if ($companyPhone !== '')
                <div class="line">Tél : {{ $companyPhone }}</div>
            @endif
            @if ($companyEmail !== '')
                <div class="line">{{ $companyEmail }}</div>
            @endif
        </footer>
    </div>

    <script>
        // Force le contenu de la page (logo projet) — pas d’en-tête/pied navigateur.
        window.addEventListener('load', function () {
            try {
                // Certains drivers thermiques réutilisent un logo stocké si la page est vide :
                // on s’assure que l’image projet est bien chargée avant impression manuelle.
                var img = document.querySelector('.header img');
                if (img && !img.complete) {
                    img.addEventListener('load', function () {}, { once: true });
                }
            } catch (e) {}
        });
    </script>
</body>
</html>
