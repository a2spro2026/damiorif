<?php

namespace App\Support;

use App\Models\Produit;

class ProduitReferenceService
{
    /**
     * Enregistre les lignes d'un bon d'achat comme références produits.
     *
     * @param  array<int, array{ref?: string|null, designation: string}>  $lignes
     */
    public static function syncFromBonAchatLignes(array $lignes): void
    {
        foreach ($lignes as $ligne) {
            self::remember(
                $ligne['ref'] ?? null,
                $ligne['designation'] ?? ''
            );
        }
    }

    /**
     * @return array<int, array{ref: string, designation: string}>
     */
    public static function catalogue(): array
    {
        return Produit::query()
            ->orderBy('ref_produit')
            ->get(['ref_produit', 'nom_produit'])
            ->map(fn (Produit $p) => [
                'ref' => $p->ref_produit,
                'designation' => $p->nom_produit,
            ])
            ->all();
    }

    public static function remember(?string $ref, string $designation): void
    {
        $designation = trim($designation);
        if ($designation === '') {
            return;
        }

        $ref = trim((string) $ref);
        $hasRef = $ref !== '' && $ref !== '—';

        if ($hasRef) {
            Produit::query()->updateOrCreate(
                ['ref_produit' => $ref],
                [
                    'nom_produit' => $designation,
                    'date_fiche' => now()->toDateString(),
                ]
            );

            return;
        }

        $existing = Produit::query()
            ->whereRaw('LOWER(nom_produit) = ?', [mb_strtolower($designation)])
            ->first();

        if ($existing) {
            $existing->update(['date_fiche' => now()->toDateString()]);

            return;
        }

        Produit::query()->create([
            'date_fiche' => now()->toDateString(),
            'ref_produit' => Produit::nextRef(),
            'nom_produit' => $designation,
        ]);
    }
}
