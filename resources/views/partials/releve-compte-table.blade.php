@once
<style>
    .releve-compte-wrap .data-table { min-width: 980px; }
    .releve-compte-wrap .data-table th.col-op,
    .releve-compte-wrap .data-table td.col-op {
        width: 1%;
        max-width: 2.8rem;
        padding: 0.45rem 0.2rem !important;
        font-size: 0.58rem !important;
        letter-spacing: 0;
        line-height: 1.1;
    }
    .releve-compte-wrap .data-table th.col-stat,
    .releve-compte-wrap .data-table td.col-stat {
        width: 1%;
        max-width: 4.2rem;
        padding: 0.45rem 0.25rem !important;
        font-size: 0.65rem !important;
    }
    .releve-compte-wrap .op-badge {
        display: inline-block;
        padding: 0.1rem 0.22rem;
        border-radius: 5px;
        font-size: 0.56rem;
        font-weight: 700;
        background: rgba(94, 200, 179, 0.12);
        color: var(--gold-light);
        white-space: nowrap;
    }
    html[data-theme="light"] .releve-compte-wrap .op-badge {
        color: #0F766E;
        background: rgba(15, 118, 110, 0.1);
    }
    .releve-compte-wrap .empty-row td {
        text-align: center;
        color: var(--text-muted);
        padding: 2rem;
    }
</style>
@endonce
<div class="table-wrap releve-compte-wrap">
    <table class="data-table releve-compte-table">
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
                    <td class="col-op"><span class="op-badge">{{ $row['operation'] }}</span></td>
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
                <tr class="empty-row"><td colspan="14">Aucune opération pour ces critères.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
