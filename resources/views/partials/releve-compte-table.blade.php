<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Opération</th>
                <th>Date</th>
                <th>N° Bon</th>
                <th>{{ $tiersLabel }}</th>
                <th>Débit</th>
                <th>Crédit</th>
                <th>Type</th>
                <th>Bnq</th>
                <th>Tiré</th>
                <th>Mnt</th>
                <th>Payé</th>
                <th>Imp</th>
                <th>Repo</th>
                <th>Dévali</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td><span class="op-badge">{{ $row['operation'] }}</span></td>
                    <td>{{ $row['date']?->format('d/m/Y') }}</td>
                    <td>{{ $row['numero_bon'] }}</td>
                    <td>{{ $row['tiers'] }}</td>
                    <td class="amt">{{ $row['debit'] ? number_format($row['debit'], 2, ',', ' ') : '—' }}</td>
                    <td class="amt">{{ $row['credit'] ? number_format($row['credit'], 2, ',', ' ') : '—' }}</td>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ $row['banque'] }}</td>
                    <td>{{ $row['tire'] }}</td>
                    <td class="amt">{{ number_format($row['montant'], 2, ',', ' ') }}</td>
                    <td class="amt">{{ $row['paye'] !== null ? number_format($row['paye'], 2, ',', ' ') : '—' }}</td>
                    <td class="amt">{{ $row['imp'] !== null ? number_format($row['imp'], 2, ',', ' ') : '—' }}</td>
                    <td class="amt">{{ $row['repo'] !== null ? number_format($row['repo'], 2, ',', ' ') : '—' }}</td>
                    <td class="amt">{{ $row['devali'] !== null ? number_format($row['devali'], 2, ',', ' ') : '—' }}</td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="14">Aucune opération pour ces critères.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
