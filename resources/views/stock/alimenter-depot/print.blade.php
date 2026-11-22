<!DOCTYPE html>
<html lang="fr" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facture de charge {{ $mvt->numero }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 20px;
            color: #111;
            background: #fff;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.4;
        }
        .no-print {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .no-print button, .no-print a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 14px;
            border: 1px solid #111;
            border-radius: 10px;
            background: #0f766e;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }
        .no-print a.ghost {
            background: #fff;
            color: #111;
        }
        .sheet { max-width: 900px; margin: 0 auto; }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 3px solid #111;
            margin-bottom: 14px;
        }
        .brand-block { display: flex; align-items: center; gap: 12px; }
        .brand-block img {
            width: 88px;
            height: auto;
            object-fit: contain;
        }
        .brand-name {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .doc-title {
            text-align: right;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .doc-sub { text-align: right; font-size: 12px; color: #333; margin-top: 4px; }
        .meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 24px;
            margin-bottom: 16px;
        }
        .meta .row { display: flex; justify-content: space-between; gap: 10px; padding: 2px 0; }
        .meta .label { font-weight: 700; white-space: nowrap; }
        .meta .value { text-align: right; word-break: break-word; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        th, td {
            border: 1px solid #222;
            padding: 8px 7px;
            font-size: 12px;
            vertical-align: top;
        }
        th {
            background: #111;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 11px;
            text-align: center;
        }
        td.num, th.num { text-align: right; white-space: nowrap; }
        td.center, th.center { text-align: center; }
        td.des { text-align: left; }
        .totals {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 24px;
            font-weight: 800;
            font-size: 14px;
        }
        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 2px solid #111;
            text-align: center;
            font-size: 12px;
            line-height: 1.5;
        }
        .footer .line { word-break: break-word; }
        @page { margin: 12mm; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/logo.png');
    $logoSrc = is_file($logoPath)
        ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
        : asset('images/logo.png');
    $companyName = (string) config('company.name', 'DAMIO-RIF');
    $companyPhone = trim((string) config('company.phone', ''));
    $fmt = fn ($v) => number_format((float) $v, 2, ',', ' ');
    $qteTotale = (float) $mvt->lignes->sum('quantite');
@endphp

    <div class="no-print">
        <button type="button" onclick="window.print()">Imprimer</button>
        <a class="ghost" href="{{ route('stock.alimenter_depot') }}">Retour Alimenter Dépôt</a>
    </div>

    <div class="sheet">
        <header class="header">
            <div class="brand-block">
                <img src="{{ $logoSrc }}" alt="{{ $companyName }}">
                <div class="brand-name">{{ $companyName }}</div>
            </div>
            <div>
                <div class="doc-title">Facture de charge</div>
                <div class="doc-sub">N° {{ $mvt->numero }}</div>
            </div>
        </header>

        <div class="meta">
            <div>
                <div class="row"><span class="label">Date</span><span class="value">{{ $mvt->date_mouvement?->format('d/m/Y') }}</span></div>
                <div class="row"><span class="label">N° bon</span><span class="value">{{ $mvt->numero }}</span></div>
                <div class="row"><span class="label">Saisi par</span><span class="value">{{ $mvt->user_name ?: '—' }}</span></div>
            </div>
            <div>
                <div class="row"><span class="label">Dépôt source</span><span class="value">{{ $centralLabel }}</span></div>
                <div class="row"><span class="label">Dépôt destination</span><span class="value">{{ $depotLabel }}</span></div>
                <div class="row"><span class="label">Ville destination</span><span class="value">{{ $depotCity }}</span></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:12%">Réf</th>
                    <th>Désignation</th>
                    <th class="num" style="width:12%">Qté</th>
                    <th class="num" style="width:14%">Prix/U</th>
                    <th class="num" style="width:16%">Sous-Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mvt->lignes as $ligne)
                    <tr>
                        <td class="center">{{ $ligne->ref_produit ?: '—' }}</td>
                        <td class="des" dir="auto">{{ $ligne->designation }}</td>
                        <td class="num">{{ $fmt($ligne->quantite) }}</td>
                        <td class="num">{{ $fmt($ligne->prix_unitaire ?? 0) }}</td>
                        <td class="num">{{ $fmt($ligne->sous_total ?? 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center">Aucun article.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals">
            <span>Qté : {{ $fmt($qteTotale) }}</span>
            <span>Montant : {{ $fmt($montant) }} MAD</span>
        </div>

        <footer class="footer">
            @if ($companyPhone !== '')
                <div class="line">Mobile : {{ $companyPhone }}</div>
            @endif
            <div class="line">Dépôt source : {{ $centralLabel }}</div>
            <div class="line">Dépôt destination : {{ $depotLabel }} ({{ $depotCity }})</div>
        </footer>
    </div>

    @if (! empty($autoPrint))
        <script>window.addEventListener('load', function () { window.print(); });</script>
    @endif
</body>
</html>
