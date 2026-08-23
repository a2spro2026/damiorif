<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bon Commande {{ $commande->numero }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; padding: 24px; }
        h1 { color: #54000b; margin-bottom: 4px; font-size: 1.4rem; }
        .meta { margin-bottom: 20px; color: #444; line-height: 1.6; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 16px; overflow: hidden; border-radius: 10px; }
        th, td { border: 1px solid #d9c48a; padding: 10px 8px; text-align: center; font-size: 13px; vertical-align: middle; }
        th {
            background: linear-gradient(180deg, #A8E6D8 0%, #5EC8B3 48%, #2A9B86 100%);
            color: #2d0006;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 11px;
        }
        td { color: #333; }
        tbody tr:nth-child(even) td { background: #faf6eb; }
        .totals { margin-top: 16px; text-align: right; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Imprimer</button>
    <h1>Bon Commande Dépôt — {{ $commande->numero }}</h1>
    <div class="meta">
        <div>Date : {{ $commande->date_commande?->format('d/m/Y') }}</div>
        <div>Dépôt demandeur : {{ $depots[$commande->depot_demandeur] ?? $commande->depot_demandeur }}</div>
        <div>Fournisseur : Dépôt DamioRif</div>
        <div>Statut : {{ $statuts[$commande->statut] ?? $commande->statut }}</div>
        @if ($commande->numero_bon_charge)
            <div>Bon de charge : {{ $commande->numero_bon_charge }} ({{ $commande->date_bon_charge?->format('d/m/Y') }})</div>
        @endif
        @if ($commande->note)
            <div>Note : {{ $commande->note }}</div>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>Réf</th>
                <th>Désignation</th>
                <th>Qté</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($commande->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->ref ?: '—' }}</td>
                    <td>{{ $ligne->designation }}</td>
                    <td>{{ number_format((float) $ligne->qte_demandee, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="totals">
        Quantité totale : {{ number_format($commande->qteTotale(), 2, ',', ' ') }}
    </div>
</body>
</html>
