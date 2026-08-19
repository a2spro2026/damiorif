<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Réglement {{ $reglement->numero }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; padding: 24px; }
        h1 { color: #54000b; margin-bottom: 4px; }
        .meta { margin-bottom: 20px; color: #444; }
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
    <h1>Réglement Achat {{ $reglement->numero }}</h1>
    <div class="meta">
        <div>Date : {{ $reglement->date_reglement?->format('d/m/Y') }}</div>
        <div>Fournisseur : {{ $reglement->nom_fournisseur }}</div>
        <div>Type Régl : {{ $typesReglement[$reglement->type_reglement] ?? ($reglement->type_reglement ?: '—') }}</div>
        <div>Banque : {{ $reglement->banque ?: '—' }}</div>
        <div>Nom Tiré : {{ $reglement->nom_tire ?: '—' }}</div>
        <div>Date Décaissement : {{ $reglement->date_decaissement?->format('d/m/Y') ?: '—' }}</div>
        <div>Statut : {{ $statuts[$reglement->statut] ?? ($reglement->statut ?: '—') }}</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Bon N°</th>
                <th>Montant appliqué</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reglement->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->numero_bon }}</td>
                    <td>{{ number_format((float) $ligne->montant_applique, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="totals">
        Montant : {{ number_format((float) $reglement->montant, 2, ',', ' ') }}
    </div>
</body>
</html>
