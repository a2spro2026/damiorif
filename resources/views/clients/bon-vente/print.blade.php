<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bon {{ $bon->numero_bon }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; padding: 24px; }
        h1 { color: #54000b; margin-bottom: 4px; }
        .meta { margin-bottom: 20px; color: #444; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 16px; overflow: hidden; border-radius: 10px; }
        th, td { border: 1px solid #d9c48a; padding: 10px 8px; text-align: center; font-size: 13px; vertical-align: middle; }
        th {
            background: linear-gradient(180deg, #e8d5a8 0%, #c9a45c 48%, #a8863f 100%);
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
    <h1>Bon Vente {{ $bon->numero_bon }}</h1>
    <div class="meta">
        <div>Date : {{ $bon->date_bon?->format('d/m/Y') }}</div>
        <div>Client : {{ $bon->nom_client }} (ID {{ $bon->client_id }})</div>
        <div>Ville : {{ $bon->ville ?: '—' }}</div>
        <div>Type Régl : {{ $typesReglement[$bon->type_reglement] ?? ($bon->type_reglement ?: '—') }}</div>
        <div>Echéance : {{ \App\Support\Echeances::label($bon->echeance !== null ? (string) $bon->echeance : null) }}</div>
        <div>Dépôt : {{ $depots[$bon->depot] ?? ($bon->depot ?: '—') }}</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Réf</th>
                <th>Désignation</th>
                <th>Famille</th>
                <th>Catégorie</th>
                <th>Qte</th>
                <th>P/U</th>
                <th>Sous-Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bon->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->ref }}</td>
                    <td>{{ $ligne->designation }}</td>
                    <td>{{ $ligne->famille }}</td>
                    <td>{{ $ligne->categorie }}</td>
                    <td>{{ number_format((float) $ligne->qte, 2, ',', ' ') }}</td>
                    <td>{{ number_format((float) $ligne->prix_unitaire, 2, ',', ' ') }}</td>
                    <td>{{ number_format((float) $ligne->sous_total, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="totals">
        Qte : {{ number_format((float) $bon->qte_totale, 2, ',', ' ') }} —
        Montant : {{ number_format((float) $bon->montant, 2, ',', ' ') }} —
        Solde : {{ number_format((float) $bon->solde, 2, ',', ' ') }}
    </div>
</body>
</html>
