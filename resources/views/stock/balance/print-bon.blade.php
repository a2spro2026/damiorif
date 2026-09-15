<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bon {{ $mvt->numero }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; padding: 24px; }
        h1 { color: #54000b; margin-bottom: 4px; }
        .meta { margin-bottom: 20px; color: #444; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 16px; overflow: hidden; border-radius: 10px; }
        th, td { border: 1px solid #d9c48a; padding: 10px 8px; text-align: center; font-size: 13px; }
        th {
            background: linear-gradient(180deg, #A8E6D8 0%, #5EC8B3 48%, #2A9B86 100%);
            color: #2d0006; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; font-size: 11px;
        }
        tbody tr:nth-child(even) td { background: #faf6eb; }
        .totals { margin-top: 16px; text-align: right; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Imprimer / PDF</button>
    <h1>Bon d’alimentation {{ $mvt->numero }}</h1>
    <div class="meta">
        <div>Date : {{ $mvt->date_mouvement?->format('d/m/Y') }}</div>
        <div>De : {{ $centralLabel }}</div>
        <div>Vers : {{ $depotLabel }}</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Réf</th>
                <th>Désignation</th>
                <th>Qté</th>
                <th>Prix/U</th>
                <th>Sous-Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mvt->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->ref_produit ?: '—' }}</td>
                    <td>{{ $ligne->designation }}</td>
                    <td>{{ number_format((float) $ligne->quantite, 2, ',', ' ') }}</td>
                    <td>{{ number_format((float) ($ligne->prix_unitaire ?? 0), 2, ',', ' ') }}</td>
                    <td>{{ number_format((float) ($ligne->sous_total ?? 0), 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="totals">
        Montant : {{ number_format($montant, 2, ',', ' ') }} MAD —
        Solde : {{ number_format($montant, 2, ',', ' ') }} MAD
    </div>
    @if (!empty($autoPrint))
        <script>window.addEventListener('load', function () { window.print(); });</script>
    @endif
</body>
</html>
