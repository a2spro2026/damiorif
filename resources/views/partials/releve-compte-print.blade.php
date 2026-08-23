<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} — Impression</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; padding: 24px; font-size: 12px; }
        h1 { color: #54000b; margin-bottom: 4px; font-size: 20px; }
        .meta { margin-bottom: 16px; color: #444; line-height: 1.5; }
        .kpi-row { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
        .kpi { border: 1px solid #d9c48a; border-radius: 8px; padding: 10px 14px; min-width: 140px; }
        .kpi-label { font-size: 10px; text-transform: uppercase; color: #666; letter-spacing: .05em; }
        .kpi-value { font-size: 16px; font-weight: 700; color: #54000b; margin-top: 4px; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 8px; overflow: hidden; border-radius: 8px; }
        th, td { border: 1px solid #d9c48a; padding: 6px 5px; text-align: center; font-size: 10px; vertical-align: middle; white-space: nowrap; }
        th {
            background: linear-gradient(180deg, #A8E6D8 0%, #5EC8B3 48%, #2A9B86 100%);
            color: #2d0006;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 9px;
        }
        td { color: #333; }
        tbody tr:nth-child(even) td { background: #faf6eb; }
        .amt { text-align: right; }
        th.col-op, td.col-op {
            width: 1%;
            max-width: 28px;
            padding: 4px 2px !important;
            font-size: 8px !important;
        }
        th.col-stat, td.col-stat {
            width: 1%;
            max-width: 42px;
            padding: 4px 3px !important;
            font-size: 8px !important;
        }
        .toolbar { margin-bottom: 16px; display: flex; gap: 10px; }
        .btn { padding: 8px 14px; border-radius: 6px; border: 1px solid #2A9B86; background: #5EC8B3; color: #2d0006; font-weight: 700; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-ghost { background: #fff; color: #54000b; border-color: #d9c48a; }
        @media print { .no-print { display: none; } body { padding: 12px; } }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button class="btn" onclick="window.print()">Imprimer</button>
        <a href="{{ route($indexRoute, request()->query()) }}" class="btn btn-ghost">Fermer</a>
    </div>

    <h1>{{ $title }}</h1>
    <div class="meta">
        @if ($depotLabel ?? null)
            <div>Dépôt : {{ $depotLabel }}</div>
        @endif
        @if ($selectedMois ?? null)
            <div>Mois : {{ collect($monthOptions)->firstWhere('value', $selectedMois)['label'] ?? $selectedMois }}</div>
        @else
            <div>Mois : Tous</div>
        @endif
        @if ($selectedTiersName ?? null)
            <div>{{ $tiersLabel }} : {{ $selectedTiersName }}</div>
        @else
            <div>{{ $tiersLabel }} : Tous</div>
        @endif
        <div>Imprimé le {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="kpi-row">
        <div class="kpi">
            <div class="kpi-label">{{ $totalDebitLabel ?? 'Total Achats' }}</div>
            <div class="kpi-value">{{ number_format($totalAchats, 2, ',', ' ') }} MAD</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Total Payé</div>
            <div class="kpi-value">{{ number_format($totalPaye, 2, ',', ' ') }} MAD</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Total Solde</div>
            <div class="kpi-value">{{ number_format($totalSolde, 2, ',', ' ') }} MAD</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-op">Op.</th>
                <th>Date</th>
                <th>N° Bon</th>
                <th>{{ $tiersLabel }}</th>
                <th>Débit</th>
                <th>Crédit</th>
                <th>Type</th>
                <th>Bnq</th>
                <th>Tiré</th>
                <th>Mnt</th>
                <th class="col-stat">Payé</th>
                <th class="col-stat">Imp</th>
                <th class="col-stat">Rep</th>
                <th class="col-stat">Dév</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td class="col-op">{{ $row['operation'] }}</td>
                    <td>{{ $row['date']?->format('d/m/Y') }}</td>
                    <td>{{ $row['numero_bon'] }}</td>
                    <td>{{ $row['tiers'] }}</td>
                    <td class="amt">{{ $row['debit'] ? number_format($row['debit'], 2, ',', ' ') : '—' }}</td>
                    <td class="amt">{{ $row['credit'] ? number_format($row['credit'], 2, ',', ' ') : '—' }}</td>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ $row['banque'] }}</td>
                    <td>{{ $row['tire'] }}</td>
                    <td class="amt">{{ number_format($row['montant'], 2, ',', ' ') }}</td>
                    <td class="col-stat amt">{{ $row['paye'] !== null ? number_format($row['paye'], 2, ',', ' ') : '—' }}</td>
                    <td class="col-stat amt">{{ $row['imp'] !== null ? number_format($row['imp'], 2, ',', ' ') : '—' }}</td>
                    <td class="col-stat amt">{{ $row['repo'] !== null ? number_format($row['repo'], 2, ',', ' ') : '—' }}</td>
                    <td class="col-stat amt">{{ $row['devali'] !== null ? number_format($row['devali'], 2, ',', ' ') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="14">Aucune opération pour ces critères.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
