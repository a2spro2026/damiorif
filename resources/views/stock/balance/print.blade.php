<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Balance alimentation</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; padding: 24px; }
        h1 { color: #54000b; margin-bottom: 4px; }
        .meta { margin-bottom: 16px; color: #444; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 12px; overflow: hidden; border-radius: 10px; }
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
    <button class="no-print" onclick="window.print()">Imprimer</button>
    <h1>Balance — Bons d’alimentation</h1>
    <div class="meta">
        @if ($depotLabel)
            <div>Dépôt : {{ $depotLabel }}</div>
        @endif
        @if ($mois)
            <div>Mois : {{ $mois }}</div>
        @endif
        @if ($numero)
            <div>N° Bon : {{ $numero }}</div>
        @endif
        <div>{{ $rows->count() }} bon(s)</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>N° Bon Alimentation</th>
                @if ($isCentral)
                    <th>Dépôt</th>
                @endif
                <th>Montant</th>
                <th>Solde</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['numero'] }}</td>
                    @if ($isCentral)
                        <td>{{ $row['depot_label'] }}</td>
                    @endif
                    <td>{{ number_format($row['montant'], 2, ',', ' ') }}</td>
                    <td>{{ number_format($row['solde'], 2, ',', ' ') }}</td>
                </tr>
            @empty
                <tr><td colspan="{{ $isCentral ? 5 : 4 }}">Aucun bon.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="totals">
        Montant : {{ number_format($totalMontant, 2, ',', ' ') }} MAD —
        Solde : {{ number_format($totalSolde, 2, ',', ' ') }} MAD
    </div>
</body>
</html>
